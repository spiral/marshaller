<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Support;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class DateTime
{
    public static function parse(
        string|\DateTimeInterface $time = null,
        \DateTimeZone|string $tz = null,
        string $class = \DateTimeInterface::class
    ): \DateTimeInterface {
        if (\is_string($time) && $matched = self::extractRfc3339Accuracy($time)) {
            [$datetime, $accuracy] = $matched;

            // Note: PHP does not support accuracy greater than 8 (microseconds)
            if (\strlen($accuracy) > 8) {
                $time = \sprintf('%s.%sZ', $datetime, \substr($accuracy, 0, 8));
            }
        }

        return match ($class) {
            \DateTimeImmutable::class => new \DateTimeImmutable($time, $tz),
            \DateTime::class => new \DateTime($time, $tz),
            CarbonImmutable::class => CarbonImmutable::parse($time, $tz),
            default => Carbon::parse($time, $tz),
        };
    }

    /**
     * Split date in RFC3339 format to "date" and "accuracy" array or
     * return {@see null} in the case that the passed time string is not valid
     * RFC3339 or ISO8601 string.
     *
     * TODO: This match function can only parse the "Z" timezone, and in the
     *       case of an explicit timezone "+00:00" this case will be ignored.
     *
     * @return null|array{0: string, 1: string}
     */
    private static function extractRfc3339Accuracy(string $time): ?array
    {
        $likeRfc3339WithAccuracy = \str_ends_with($time, 'Z')
            && \substr_count($time, '.') === 1
        ;

        if ($likeRfc3339WithAccuracy) {
            // $date is "YYYY-mm-dd HH:ii:ss"
            // $accuracy is "PPPP+" where P is digit of [milli/micro/nano] seconds
            [$date, $accuracy] = \explode('.', \substr($time, 0, -1));

            if (\ctype_digit($accuracy)) {
                return [$date, $accuracy];
            }
        }

        return null;
    }
}
