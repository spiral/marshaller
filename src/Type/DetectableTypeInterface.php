<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

interface DetectableTypeInterface
{
    public static function match(\ReflectionNamedType $type): bool;
}
