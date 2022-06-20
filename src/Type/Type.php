<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use Spiral\Marshaller\MarshallerInterface;
use Spiral\Marshaller\Internal\Support\Inheritance;

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
    protected function ofType(MarshallerInterface $marshaller, string $name): ?TypeInterface
    {
        return Inheritance::implements($name, TypeInterface::class)
            ? new $name($marshaller)
            : new ObjectType($marshaller, $name)
        ;
    }
}
