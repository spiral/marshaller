<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType;

final class ChildDto
{
    public function __construct(
        public string $foo,
    ) {
    }
}
