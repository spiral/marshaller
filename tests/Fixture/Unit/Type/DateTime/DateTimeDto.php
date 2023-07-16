<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Type\DateTime;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spiral\Marshaller\Meta\Marshal;
use Spiral\Marshaller\Type\DateTimeType;

class DateTimeDto
{
    #[Marshal(type: DateTimeType::class)]
    public \DateTimeInterface $date1;

    #[Marshal(type: DateTimeType::class, of: \DateTimeImmutable::class, nullable: true)]
    public ?\DateTimeImmutable $date2;

    public \DateTimeImmutable $immutable;

    public \DateTime $dateTime;

    public ?Carbon $carbon;

    public ?CarbonImmutable $carbonImmutable;
}
