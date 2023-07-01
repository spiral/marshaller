<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use Spiral\Marshaller\MarshallerInterface;
use Spiral\Marshaller\MarshallingRule;
use Spiral\Marshaller\Support\Inheritance;

abstract class Type implements TypeInterface
{
    protected MarshallerInterface $marshaller;

    public function __construct(MarshallerInterface $marshaller)
    {
        $this->marshaller = $marshaller;
    }

    /**
     * @throws \ReflectionException
     */
    protected function ofType(MarshallerInterface $marshaller, MarshallingRule|string $type): ?TypeInterface
    {
        $of = $type instanceof MarshallingRule && $type->of !== null
            ? $type->of
            : null;
        $typeClass = $type instanceof MarshallingRule ? $type->type : $type;

        \assert($typeClass !== null);

        if (Inheritance::implements($typeClass, TypeInterface::class)) {
            return $of === null
                ? new $typeClass($marshaller)
                : new $typeClass($marshaller, $of);
        }

        return new ObjectType($marshaller, $typeClass);
    }
}
