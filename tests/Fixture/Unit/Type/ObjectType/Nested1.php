<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType;

final class Nested1
{
    public function __construct(
        public Nested2 $child,
    ) {
    }
}
