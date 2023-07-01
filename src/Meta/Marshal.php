<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Meta;

use Spiral\Attributes\NamedArgumentConstructor;
use Spiral\Marshaller\MarshallingRule;
use Spiral\Marshaller\Type\NullableType;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({ "PROPERTY" })
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
class Marshal extends MarshallingRule
{
    /**
     * @param class-string|null $type
     * @param null|Marshal|string $of
     */
    public function __construct(
        ?string $name = null,
        ?string $type = null,
        self|string|null $of = null,
        public bool $nullable = false,
    ) {
        parent::__construct($name, $type, $of);
    }

    public function toTypeDto(): MarshallingRule
    {
        if (!$this->nullable) {
            return $this;
        }

        return new MarshallingRule(
            $this->name,
            NullableType::class,
            $this->of === null ? $this->type : $this,
        );
    }
}
