<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Meta;

use Spiral\Attributes\NamedArgumentConstructor;
use Spiral\Marshaller\Type\ArrayType;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({ "PROPERTY", "METHOD" })
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class MarshalArray extends Marshal
{
    public function __construct(
        string $name = null,
        string $of = null,
        bool $nullable = true,
    ) {
        parent::__construct($name, ArrayType::class, $of, $nullable);
    }
}
