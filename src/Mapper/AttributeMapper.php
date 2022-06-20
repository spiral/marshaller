<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Mapper;

use Spiral\Attributes\ReaderInterface;
use Spiral\Marshaller\Meta\Marshal;
use Spiral\Marshaller\Meta\Scope;
use Spiral\Marshaller\Type\TypeInterface;
use Spiral\Marshaller\TypeFactoryInterface;

/**
 * @psalm-import-type Getter from MapperInterface
 * @psalm-import-type Setter from MapperInterface
 */
class AttributeMapper implements MapperInterface
{
    private \ReflectionClass $class;
    private ReaderInterface $reader;

    /** @var array<string, Getter> */
    private array $getters = [];

    /** @var array<string, Setter> */
    private array $setters = [];

    private Scope $scope;
    private TypeFactoryInterface $factory;

    public function __construct(\ReflectionClass $class, TypeFactoryInterface $factory, ReaderInterface $reader)
    {
        $this->class = $class;
        $this->reader = $reader;
        $this->factory = $factory;
        $this->scope = $this->getScope();

        foreach ($this->getPropertyMappings($this->scope) as $property => $marshal) {
            $type = $this->detectType($property, $marshal);
            $name = $property->getName();

            $this->getters[$marshal->name] = $this->createGetter($name, $type);
            $this->setters[$marshal->name] = $this->createSetter($name, $type);
        }
    }

    public function isCopyOnWrite(): bool
    {
        return $this->scope->copyOnWrite;
    }

    public function getGetters(): iterable
    {
        return $this->getters;
    }

    public function getSetters(): iterable
    {
        return $this->setters;
    }

    private function getScope(): Scope
    {
        return $this->reader->firstClassMetadata($this->class, Scope::class) ?? new Scope();
    }

    /**
     * @return iterable<\ReflectionProperty, Marshal>
     */
    private function getPropertyMappings(Scope $scope): iterable
    {
        foreach ($this->class->getProperties() as $property) {
            /** @var Marshal $marshal */
            $marshal = $this->reader->firstPropertyMetadata($property, Marshal::class);
            $name = $property->getName();

            // Has marshal attribute
            if ($marshal === null && !$this->isValidScope($property, $scope)) {
                continue;
            }

            $marshal ??= new Marshal();
            $marshal->name ??= $name;

            yield $property => $marshal;
        }
    }

    private function isValidScope(\ReflectionProperty $property, Scope $scope): bool
    {
        return ($property->getModifiers() & $scope->properties) === $scope->properties;
    }

    private function detectType(\ReflectionProperty $property, Marshal $meta): ?TypeInterface
    {
        $type = $meta->type ?? $this->factory->detect($property->getType());

        if ($type === null) {
            return null;
        }

        return $this->factory->create($type, $meta->of ? [$meta->of] : []);
    }

    private function createGetter(string $name, ?TypeInterface $type): \Closure
    {
        return function () use ($name, $type) {
            try {
                $result = $this->$name;
            } catch (\Error $_) {
                return null;
            }

            return $type ? $type->serialize($result) : $result;
        };
    }

    private function createSetter(string $name, ?TypeInterface $type): \Closure
    {
        return function ($value) use ($name, $type): void {
            try {
                $source = $this->$name;
            } catch (\Error $_) {
                $source = null;
            }

            $this->$name = $type ? $type->parse($value, $source) : $value;
        };
    }
}
