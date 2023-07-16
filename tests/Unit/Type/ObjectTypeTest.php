<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Unit\Type;

use Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType\ChildDto;
use Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType\Nested1;
use Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType\Nested2;
use Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType\Nested3;
use Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType\NestedParent;
use Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType\ParentDto;
use Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType\ReadonlyProperty;
use Spiral\Marshaller\Tests\Fixture\Unit\Type\ObjectType\StdClassObjectProp;
use Spiral\Marshaller\Tests\Unit\TestCase;
use Spiral\Marshaller\Type\ObjectType;

final class ObjectTypeTest extends TestCase
{
    public function testReflectionTypeMarshal(): void
    {
        $dto = new ParentDto(
            new ChildDto('foo')
        );

        $result = $this->marshaller->marshal($dto);

        $this->assertEquals(['child' => ['foo' => 'foo']], $result);
    }

    public function testReflectionTypeUnmarshal(): void
    {
        $dto = $this->marshaller->unmarshal([
            'child' => ['foo' => 'bar'],
        ], (new \ReflectionClass(ParentDto::class))->newInstanceWithoutConstructor());

        $this->assertEquals(new ParentDto(
            new ChildDto('bar')
        ), $dto);
    }

    public function testReadonlyMarshal(): void
    {
        $dto = new ReadonlyProperty(
            new ChildDto('foo')
        );

        $result = $this->marshaller->marshal($dto);

        $this->assertEquals(['child' => ['foo' => 'foo']], $result);
    }

    public function testStdClassParamUnmarshal(): void
    {
        $dto = $this->marshaller->unmarshal([
            'object' => ['foo' => 'bar'],
            'class' => ['foo' => 'bar'],
        ], (new \ReflectionClass(StdClassObjectProp::class))->newInstanceWithoutConstructor());

        $this->assertEquals(new StdClassObjectProp(
            (object)['foo' => 'bar'],
            (object)['foo' => 'bar'],
        ), $dto);
    }

    public function testStdClassUnmarshal(): void
    {
        $dto = $this->marshaller->unmarshal([
            'object' => ['foo' => 'bar'],
            'class' => ['foo' => 'bar'],
        ], new \stdClass());

        $this->assertEquals((object)[
            'object' => ['foo' => 'bar'],
            'class' => ['foo' => 'bar'],
        ], $dto);
    }

    public function testReadonlyUnmarshal(): void
    {
        $dto = $this->marshaller->unmarshal([
            'child' => ['foo' => 'bar'],
        ], (new \ReflectionClass(ReadonlyProperty::class))->newInstanceWithoutConstructor());

        $this->assertEquals(new ReadonlyProperty(
            new ChildDto('bar')
        ), $dto);
    }

    public function testNestedMarshal(): void
    {
        $dto = new NestedParent(
            new Nested1(new Nested2(new Nested3('bar')))
        );

        $marshal = $this->marshaller->marshal($dto);

        $this->assertSame(['child' => ['child' => ['child' => ['value' => 'bar']]]], $marshal);
    }

    public function testNestedUnmarshal(): void
    {
        $dto = new NestedParent(
            new Nested1(new Nested2(new Nested3('bar')))
        );

        $unmarshal = $this->marshaller->unmarshal(
            ['child' => ['child' => ['child' => ['value' => 'bar']]]],
            (new \ReflectionClass(NestedParent::class))->newInstanceWithoutConstructor(),
        );

        $this->assertEquals($dto, $unmarshal);
    }

    protected function getTypeMatchers(): array
    {
        return [
            ObjectType::class,
        ];
    }
}
