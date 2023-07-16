<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType;

final class Nested2
{
    public function __construct(
        public Nested3 $child,
    ) {
    }
}
