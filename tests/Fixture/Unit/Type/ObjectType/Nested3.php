<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType;

final class Nested3
{
    public function __construct(
        public string $value = 'foo',
    ) {
    }
}
