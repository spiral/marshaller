<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\ArrayType;

use Spiral\Marshaller\Meta\MarshalArray;

class ArrayDto
{
    #[MarshalArray(name: 'foo', nullable: false)]
    public array $foo;

    #[MarshalArray(name: 'bar', nullable: true)]
    public ?array $bar;

    #[MarshalArray(name: 'baz', nullable: true)]
    public ?array $baz;

    public array $autoArray;

    public ?array $nullableFoo;

    public ?array $nullableBar;

    public iterable $iterable;

    public ?iterable $iterableNullable;
}
