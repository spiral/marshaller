<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Marshaller;

use Ramsey\Uuid\UuidInterface;

final class Uuid
{
    public function __construct(
        public UuidInterface $uuid,
        public ?UuidInterface $nullableUuid = null
    ) {
    }
}
