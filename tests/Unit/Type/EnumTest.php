<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Unit\Type;

use Spiral\Marshaller\Tests\Fixture\Unit\Type\EnumType\EnumDto;
use Spiral\Marshaller\Tests\Fixture\Unit\Type\EnumType\ScalarEnum;
use Spiral\Marshaller\Tests\Fixture\Unit\Type\EnumType\SimpleEnum;
use Spiral\Marshaller\Tests\Unit\TestCase;
use Spiral\Marshaller\Type\EnumType;

final class EnumTest extends TestCase
{
    public function testMarshal(): void
    {
        $dto = new EnumDto();
        $dto->simpleEnum = SimpleEnum::TEST;
        $dto->scalarEnum = ScalarEnum::TESTED_ENUM;
        $dto->autoSimpleEnum = SimpleEnum::TEST;
        $dto->autoScalarEnum = ScalarEnum::TESTED_ENUM;
        $dto->nullable = null;

        $result = $this->marshaller->marshal($dto);
        $this->assertSame(['name' => 'TEST'], $result['simpleEnum']);
        $this->assertSame(['name' => 'TESTED_ENUM', 'value' => 'tested'], $result['scalarEnum']);
        $this->assertSame(['name' => 'TEST'], $result['autoSimpleEnum']);
        $this->assertSame(['name' => 'TESTED_ENUM', 'value' => 'tested'], $result['autoScalarEnum']);
        $this->assertNull($result['nullable']);
    }

    public function testMarshalEnumIntoNullable(): void
    {
        $dto = new EnumDto();
        $dto->nullable = ScalarEnum::TESTED_ENUM;

        $result = $this->marshaller->marshal($dto);
        $this->assertSame(['name' => 'TESTED_ENUM', 'value' => 'tested'], $result['nullable']);
    }

    public function testUnmarshalBackedEnumUsingScalarValue(): void
    {
        $dto = $this->marshaller->unmarshal([
            'scalarEnum' => ScalarEnum::TESTED_ENUM->value,
        ], new EnumDto());

        $this->assertSame(ScalarEnum::TESTED_ENUM, $dto->scalarEnum);
    }

    public function testUnmarshalBackedEnumUsingValueInArray(): void
    {
        $dto = $this->marshaller->unmarshal([
            'scalarEnum' => ['value' => ScalarEnum::TESTED_ENUM->value],
        ], new EnumDto());

        $this->assertSame(ScalarEnum::TESTED_ENUM, $dto->scalarEnum);
    }

    public function testUnmarshalEnumUsingNameInArray(): void
    {
        $dto = $this->marshaller->unmarshal([
            'simpleEnum' => ['name' => SimpleEnum::TEST->name],
        ], new EnumDto());

        $this->assertSame(SimpleEnum::TEST, $dto->simpleEnum);
    }

    public function testUnmarshalNonBackedEnumUsingScalarArgument(): void
    {
        try {
            $this->marshaller->unmarshal([
                'simpleEnum' => SimpleEnum::TEST->name,
            ], new EnumDto());

            $this->fail('Expected exception');
        }catch (\Throwable $e) {
            $this->assertInstanceOf(\Error::class, $e->getPrevious());
        }
    }

    public function testMarshalAndUnmarshalSame(): void
    {
        $dto = new EnumDTO();
        $dto->simpleEnum = SimpleEnum::TEST;
        $dto->scalarEnum = ScalarEnum::TESTED_ENUM;
        $dto->autoSimpleEnum = SimpleEnum::TEST;
        $dto->autoScalarEnum = ScalarEnum::TESTED_ENUM;
        $dto->nullable = null;

        $result = $this->marshaller->marshal($dto);
        $unmarshal = $this->marshaller->unmarshal($result, new EnumDTO());

        $this->assertEquals($dto, $unmarshal);
    }

    public function testUnmarshalNullToNotNullable(): void
    {
        try {
            $this->marshaller->unmarshal([
                'autoSimpleEnum' => null,
            ], new EnumDto());

            $this->fail('Null value should not be allowed.');
        } catch (\Throwable $e) {
            $this->assertStringContainsString(
                '`autoSimpleEnum`',
                $e->getMessage(),
            );
            $this->assertInstanceOf(\InvalidArgumentException::class, $e->getPrevious());
            $this->assertStringContainsString(
                'Invalid Enum value',
                $e->getPrevious()->getMessage(),
            );
        }
    }

    protected function getTypeMatchers(): array
    {
        return [
            EnumType::class,
        ];
    }
}
