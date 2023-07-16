<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\UuidType;

use Ramsey\Uuid\UuidInterface;

final class PropertyType
{
    public string $string;
    public int $int;
    public float $float;
    public bool $bool;
    public array $array;
    public ?string $nullableString;
    public ?int $nullableInt;
    public ?float $nullableFloat;
    public ?bool $nullableBool;
    public ?array $nullableArray;
    public UuidInterface $uuid;
    public ?UuidInterface $nullableUuid;
}
