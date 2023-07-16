<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Unit;

use Ramsey\Uuid\Rfc4122\UuidV4;
use Spiral\Marshaller\Tests\Fixture\Unit\Marshaller\A;
use Spiral\Marshaller\Tests\Fixture\Unit\Marshaller\B;
use Spiral\Marshaller\Tests\Fixture\Unit\Marshaller\Uuid;

final class MarshallerTest extends TestCase
{
    public function testNestedNullableObjectWasSerialized(): void
    {
        $this->assertEquals(['x' => 'x', 'b' => null], $this->marshaller->marshal(new A('x')));
    }

    public function testNestedNotNullableObjectWasSerialized(): void
    {
        $this->assertEquals(
            ['x' => 'x', 'b' => ['code' => 'y', 'description' => null]],
            $this->marshaller->marshal(new A('x', new B('y')))
        );
    }

    public function testMarshalUuid(): void
    {
        $this->assertSame(
            ['uuid' => 'd1fb065d-f118-477d-a62a-ef93dc7ee03f', 'nullableUuid' => null],
            $this->marshaller->marshal(new Uuid(UuidV4::fromString('d1fb065d-f118-477d-a62a-ef93dc7ee03f')))
        );

        $this->assertSame(
            [
                'uuid' => 'd1fb065d-f118-477d-a62a-ef93dc7ee03f',
                'nullableUuid' => 'c4cf52f6-32ba-428c-ae7d-25aaa4057f5b'
            ],
            $this->marshaller->marshal(new Uuid(
                UuidV4::fromString('d1fb065d-f118-477d-a62a-ef93dc7ee03f'),
                UuidV4::fromString('c4cf52f6-32ba-428c-ae7d-25aaa4057f5b'),
            ))
        );
    }

    public function testUnmarshalUuid(): void
    {
        $ref = new \ReflectionClass(Uuid::class);

        $this->assertEquals(
            new Uuid(UuidV4::fromString('d1fb065d-f118-477d-a62a-ef93dc7ee03f'), null),
            $this->marshaller->unmarshal(
                ['uuid' => 'd1fb065d-f118-477d-a62a-ef93dc7ee03f', 'nullableUuid' => null],
                $ref->newInstanceWithoutConstructor()
            )
        );

        $this->assertEquals(
            new Uuid(
                UuidV4::fromString('d1fb065d-f118-477d-a62a-ef93dc7ee03f'),
                UuidV4::fromString('c4cf52f6-32ba-428c-ae7d-25aaa4057f5b'),
            ),
            $this->marshaller->unmarshal(
                [
                    'uuid' => 'd1fb065d-f118-477d-a62a-ef93dc7ee03f',
                    'nullableUuid' => 'c4cf52f6-32ba-428c-ae7d-25aaa4057f5b'
                ],
                $ref->newInstanceWithoutConstructor()
            )
        );
    }
}
