<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Meta;

use Spiral\Attributes\NamedArgumentConstructorAttribute;
use Spiral\Marshaller\Type\TypeInterface;

/**
 * @Annotation
 * @Target({ "PROPERTY" })
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Marshal implements NamedArgumentConstructorAttribute
{
    public ?string $name = null;

    /** @var class-string<TypeInterface>|null */
    public ?string $type = null;

    /** @var class-string<TypeInterface>|string|null */
    public ?string $of = null;

    /**
     * @param class-string<TypeInterface>|null $type
     * @param class-string<TypeInterface>|string|null $of
     */
    public function __construct(
        string $name = null,
        string $type = null,
        string $of = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->of = $of;
    }
}
