<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use Spiral\Marshaller\MarshallerInterface;

class ObjectType extends Type implements DetectableTypeInterface
{
    private \ReflectionClass $reflection;

    /**
     * @throws \ReflectionException
     */
    public function __construct(MarshallerInterface $marshaller, string $class = null)
    {
        $this->reflection = new \ReflectionClass($class ?? \stdClass::class);

        parent::__construct($marshaller);
    }

    public static function match(\ReflectionNamedType $type): bool
    {
        return !$type->isBuiltin();
    }

    public function parse($value, $current): object
    {
        if (is_object($value)) {
            return $value;
        }

        if ($current === null) {
            $current = $this->instance((array)$value);
        }

        return $this->marshaller->unmarshal($value, $current);
    }

    public function serialize($value): array
    {
        return $this->marshaller->marshal($value);
    }

    /**
     * @throws \ReflectionException
     */
    protected function instance(array $data): object
    {
        return $this->marshaller->unmarshal($data, $this->reflection->newInstanceWithoutConstructor());
    }
}
