<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType;

final class StdClassObjectProp
{
    public function __construct(
        public object $object,
        public \stdClass $class,
    ) {
    }
}
