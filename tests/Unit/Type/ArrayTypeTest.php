<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Unit\Type;

use Spiral\Marshaller\Tests\Fixture\Unit\Type\ArrayType\ArrayDto;
use Spiral\Marshaller\Tests\Unit\TestCase;
use Spiral\Marshaller\Type\ArrayType;

final class ArrayTypeTest extends TestCase
{
    public function testMarshalling(): void
    {
        $dto = new ArrayDto();
        $dto->foo = ['foo'];
        $dto->bar = ['bar'];
        $dto->baz = null;
        $dto->autoArray = ['foo'];
        $dto->nullableFoo = ['bar'];
        $dto->nullableBar = null;
        $dto->iterable = (static function (): \Generator {
            yield 'foo';
        })();
        $dto->iterableNullable = null;

        $result = $this->marshaller->marshal($dto);
        $this->assertSame([
            'foo' => ['foo'],
            'bar' => ['bar'],
            'baz' => null,
            'autoArray' => ['foo'],
            'nullableFoo' => ['bar'],
            'nullableBar' => null,
            'iterable' => ['foo'],
            'iterableNullable' => null,
        ], $result);
    }

    public function testUnmarshalling(): void
    {
        $dto = $this->marshaller->unmarshal([
            'foo' => ['foo'],
            'bar' => ['bar'],
            'baz' => null,
            'autoArray' => ['foo'],
            'nullableFoo' => ['bar'],
            'nullableBar' => null,
            'iterable' => ['it'],
            'iterableNullable' => ['itn'],
        ], new ArrayDto());

        $this->assertSame(['foo'], $dto->foo);
        $this->assertSame(['bar'], $dto->bar);
        $this->assertSame(null, $dto->baz);
        $this->assertSame(['foo'], $dto->autoArray);
        $this->assertSame(['bar'], $dto->nullableFoo);
        $this->assertSame(['it'], $dto->iterable);
        $this->assertSame(['itn'], $dto->iterableNullable);
        $this->assertSame(null, $dto->nullableBar);
    }

    public function testSetNullToNotNullable(): void
    {
        try {
            $this->marshaller->unmarshal([
                'foo' => null,
            ], new ArrayDto());

            $this->fail('Null value should not be allowed.');
        } catch (\Throwable $e) {
            $this->assertStringContainsString(
                '`foo`',
                $e->getMessage(),
            );
            $this->assertInstanceOf(\InvalidArgumentException::class, $e->getPrevious());
            $this->assertStringContainsString(
                'Passed value must be a type of array, but null given',
                $e->getPrevious()->getMessage(),
            );
        }
    }

    protected function getTypeMatchers(): array
    {
        return [
            ArrayType::class,
        ];
    }
}
