<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType;

final class ParentDto
{
    public function __construct(
        public ChildDto $child,
    ) {
    }
}
