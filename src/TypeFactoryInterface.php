<?php

declare(strict_types=1);

namespace Spiral\Marshaller;

use Spiral\Marshaller\Type\TypeInterface;

interface TypeFactoryInterface
{
    /**
     * @param class-string<TypeInterface> $type
     */
    public function create(string $type, array $args): ?TypeInterface;

    /**
     * @return class-string<TypeInterface>|null
     */
    public function detect(?\ReflectionType $type): ?string;
}
