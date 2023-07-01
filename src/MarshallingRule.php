<?php

declare(strict_types=1);

namespace Spiral\Marshaller;

use Spiral\Marshaller\Type\TypeInterface;

/**
 * @internal
 */
class MarshallingRule
{
    /**
     * @param class-string<TypeInterface>|null $type
     * @param self|class-string<TypeInterface>|string|null $of
     */
    public function __construct(
        public ?string $name = null,
        public ?string $type = null,
        public self|string|null $of = null,
    ) {
    }
}
