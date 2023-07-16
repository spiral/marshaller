<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType;

final class ReadonlyProperty
{
    public function __construct(
        public readonly ChildDto $child,
    ) {
    }
}
