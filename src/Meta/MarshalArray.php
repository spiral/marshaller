<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Meta;

use Spiral\Marshaller\Type\ArrayType;
use Spiral\Marshaller\Type\TypeInterface;

/**
 * @Annotation
 * @Target({ "PROPERTY", "METHOD" })
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class MarshalArray extends Marshal
{
    /**
     * @param class-string<TypeInterface>|string|null $of
     */
    public function __construct(string $name = null, string $of = null)
    {
        parent::__construct($name, ArrayType::class, $of);
    }
}
