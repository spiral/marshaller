<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Unit\Type;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spiral\Marshaller\Tests\Fixture\Unit\Type\DateTime\DateTimeDto;
use Spiral\Marshaller\Tests\Unit\TestCase;
use Spiral\Marshaller\Type\DateTimeType;

final class DateTimeTest extends TestCase
{
    public function testMarshal(): void
    {
        $dto = new DateTimeDto();
        $dto->date1 = new \DateTimeImmutable('2020-12-15 11:13:00');
        $dto->date2 = new \DateTimeImmutable('2020-12-15 11:13:01');
        $dto->immutable = new \DateTimeImmutable('2020-12-15 11:13:02');
        $dto->dateTime = new \DateTime('2020-12-15 11:13:03');
        $dto->carbon = new Carbon('2020-12-15 11:13:04');
        $dto->carbonImmutable = new CarbonImmutable('2020-12-15 11:13:05');

        $result = $this->marshaller->marshal($dto);

        $this->assertSame([
            'date1' => '2020-12-15T11:13:00+00:00',
            'date2' => '2020-12-15T11:13:01+00:00',
            'immutable' => '2020-12-15T11:13:02+00:00',
            'dateTime' => '2020-12-15T11:13:03+00:00',
            'carbon' => '2020-12-15T11:13:04+00:00',
            'carbonImmutable' => '2020-12-15T11:13:05+00:00',
        ], $result);
    }

    public function testUnmarshal(): void
    {
        $dto = new DateTimeDto();
        $dto->date1 = new \DateTimeImmutable('2020-12-15 11:13:00');
        $dto->date2 = new \DateTimeImmutable('2020-12-15 11:13:01');
        $dto->immutable = new \DateTimeImmutable('2020-12-15 11:13:02');
        $dto->dateTime = new \DateTime('2020-12-15 11:13:03');
        $dto->carbon = new Carbon('2020-12-15 11:13:04');
        $dto->carbonImmutable = new CarbonImmutable('2020-12-15 11:13:05');

        $result = $this->marshaller->unmarshal([
            'date1' => '2020-12-15T11:13:00+00:00',
            'date2' => '2020-12-15T11:13:01+00:00',
            'immutable' => '2020-12-15T11:13:02+00:00',
            'dateTime' => '2020-12-15T11:13:03+00:00',
            'carbon' => '2020-12-15T11:13:04+00:00',
            'carbonImmutable' => '2020-12-15T11:13:05+00:00',
        ], new DateTimeDto());

        $this->assertEquals($dto, $result);
    }

    protected function getTypeMatchers(): array
    {
        return [
            DateTimeType::class,
        ];
    }
}
