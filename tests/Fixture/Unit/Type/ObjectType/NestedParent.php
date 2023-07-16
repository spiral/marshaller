<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType;

final class NestedParent
{
    public function __construct(
        public Nested1 $child,
    ) {
    }
}
