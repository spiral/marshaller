<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\EnumType;

use Spiral\Marshaller\Meta\Marshal;
use Spiral\Marshaller\Type\EnumType;

class EnumDto
{
    #[Marshal(name: 'simpleEnum', type: EnumType::class, of: SimpleEnum::class)]
    public SimpleEnum $simpleEnum;

    #[Marshal(name: 'scalarEnum', type: EnumType::class, of: ScalarEnum::class)]
    public ScalarEnum $scalarEnum;

    public SimpleEnum $autoSimpleEnum;

    public ScalarEnum $autoScalarEnum;

    public ?ScalarEnum $nullable;
}
