<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Mapper;

use Spiral\Attributes\ReaderInterface;
use Spiral\Marshaller\MarshallingRule;
use Spiral\Marshaller\Meta\Marshal;
use Spiral\Marshaller\Meta\Scope;
use Spiral\Marshaller\RuleFactoryInterface;
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

    /**
     * @var array<string, Getter>
     */
    private array $getters = [];

    /**
     * @var array<string, Setter>
     */
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
     * Generates property name as key and related {@see MarshallingRule} or {@see null} (if no {@see Marshal}
     * attributes found) as value.
     *
     * @return iterable<\ReflectionProperty, MarshallingRule|null>
     */
    private function getPropertyMappings(Scope $scope): iterable
    {
        foreach ($this->class->getProperties() as $property) {
            /** @var Marshal $marshal */
            $marshal = $this->reader->firstPropertyMetadata($property, Marshal::class);

            // Has marshal attribute
            if ($marshal === null && !$this->isValidScope($property, $scope)) {
                continue;
            }

            yield $property => $marshal?->toTypeDto();
        }
    }

    private function isValidScope(\ReflectionProperty $property, Scope $scope): bool
    {
        return ($property->getModifiers() & $scope->properties) === $scope->properties;
    }

    private function detectType(\ReflectionProperty $property, ?MarshallingRule &$rule): ?TypeInterface
    {
        if ($this->factory instanceof RuleFactoryInterface) {
            $rule ??= $this->factory->makeRule($property);
        }
        $rule ??= new MarshallingRule();
        $rule->name ??= $property->getName();
        $rule->type ??= $this->factory->detect($property->getType());

        if ($rule->type === null) {
            return null;
        }

        return $this->factory->create($rule->type, $rule->of ? [$rule->of] : []);
    }

    private function createGetter(string $name, ?TypeInterface $type): \Closure
    {
        return function () use ($name, $type) {
            try {
                $result = $this->$name;
            } catch (\Error $_) {
                return null;
            }

            return $type && $result !== null ? $type->serialize($result) : $result;
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
