<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use Spiral\Marshaller\MarshallerInterface;
use Spiral\Marshaller\MarshallingRule;

/**
 * @template TClass of object
 */
class ObjectType extends Type implements DetectableTypeInterface, RuleFactoryInterface
{
    /**
     * @var \ReflectionClass<TClass>
     */
    private \ReflectionClass $reflection;

    /**
     * @param class-string<TClass>|null $class
     * @throws \ReflectionException
     */
    public function __construct(MarshallerInterface $marshaller, string $class = null)
    {
        $this->reflection = new \ReflectionClass($class ?? \stdClass::class);

        parent::__construct($marshaller);
    }

    public static function match(\ReflectionNamedType $type): bool
    {
        return !$type->isBuiltin() || $type->getName() === 'object';
    }

    public static function makeRule(\ReflectionProperty $property): ?MarshallingRule
    {
        $type = $property->getType();

        if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
            return null;
        }

        return new MarshallingRule($property->getName(), self::class, $type->getName());
    }

    /**
     * @return TClass
     */
    public function parse(mixed $value, mixed $current): object
    {
        if (\is_object($value)) {
            return $value;
        }

        if ($current === null) {
            $current = $this->emptyInstance();
        }

        if ($current::class === \stdClass::class && $this->reflection->getName() === \stdClass::class) {
            foreach ($value as $key => $val) {
                $current->$key = $val;
            }

            return $current;
        }

        return $this->marshaller->unmarshal($value, $current);
    }

    /**
     * @psalm-assert TClass $value
     */
    public function serialize(mixed $value): array
    {
        return $this->reflection->getName() === \stdClass::class
            ? (array)$value
            : $this->marshaller->marshal($value);
    }

    /**
     * @return TClass
     * @throws \ReflectionException
     */
    protected function emptyInstance(): object
    {
        return $this->reflection->newInstanceWithoutConstructor();
    }
}
