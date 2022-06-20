<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Internal\Support;

use Carbon\CarbonInterval;
use Google\Protobuf\Duration;

/**
 * @psalm-type DateIntervalFormat = DateInterval::FORMAT_*
 * @psalm-type DateIntervalValue = string | int | float | \DateInterval
 */
final class DateInterval
{
    /**
     * @var string
     */
    public const FORMAT_YEARS = 'years';

    /**
     * @var string
     */
    public const FORMAT_MONTHS = 'months';

    /**
     * @var string
     */
    public const FORMAT_WEEKS = 'weeks';

    /**
     * @var string
     */
    public const FORMAT_DAYS = 'days';

    /**
     * @var string
     */
    public const FORMAT_HOURS = 'hours';

    /**
     * @var string
     */
    public const FORMAT_MINUTES = 'minutes';

    /**
     * @var string
     */
    public const FORMAT_SECONDS = 'seconds';

    /**
     * @var string
     */
    public const FORMAT_MILLISECONDS = 'milliseconds';

    /**
     * @var string
     */
    public const FORMAT_MICROSECONDS = 'microseconds';

    /**
     * @var string
     */
    public const FORMAT_NANOSECONDS = 'nanoseconds';

    /**
     * @var string
     */
    private const ERROR_INVALID_DATETIME = 'Unrecognized date time interval format';

    /**
     * @var string
     */
    private const ERROR_INVALID_FORMAT = 'Invalid date interval format "%s", available formats: %s';

    /**
     * @var array<positive-int, DateIntervalFormat>
     */
    private const AVAILABLE_FORMATS = [
        self::FORMAT_YEARS,
        self::FORMAT_MONTHS,
        self::FORMAT_WEEKS,
        self::FORMAT_DAYS,
        self::FORMAT_HOURS,
        self::FORMAT_MINUTES,
        self::FORMAT_SECONDS,
        self::FORMAT_MILLISECONDS,
        self::FORMAT_MICROSECONDS,
        self::FORMAT_NANOSECONDS,
    ];

    /**
     * @param DateIntervalValue $interval
     * @param DateIntervalFormat $format
     */
    public static function parse($interval, string $format = self::FORMAT_MILLISECONDS): CarbonInterval
    {
        self::validateFormat($format);

        switch (true) {
            case \is_string($interval):
                return CarbonInterval::fromString($interval);

            case $interval instanceof \DateInterval:
                return CarbonInterval::instance($interval);

            case \is_int($interval):
            case \is_float($interval):
                if ($format === self::FORMAT_NANOSECONDS) {
                    return CarbonInterval::microseconds($interval / 1000);
                }

                return CarbonInterval::$format($interval);

            default:
                throw new \InvalidArgumentException(self::ERROR_INVALID_DATETIME);
        }
    }

    /**
     * @param DateIntervalValue|null $interval
     * @param DateIntervalFormat $format
     */
    public static function parseOrNull($interval, string $format = self::FORMAT_MILLISECONDS): ?CarbonInterval
    {
        if ($interval === null) {
            return null;
        }

        return self::parse($interval, $format);
    }

    /**
     * @param DateIntervalValue $interval
     */
    public static function assert($interval): bool
    {
        $isParsable = \is_string($interval) || \is_int($interval) || \is_float($interval);

        return $isParsable || $interval instanceof \DateInterval;
    }

    public static function toDuration(\DateInterval $i = null): ?Duration
    {
        if ($i === null) {
            return null;
        }

        $d = new Duration();
        $d->setSeconds(self::parse($i)->totalSeconds);

        return $d;
    }

    private static function validateFormat(string $format): void
    {
        if (!\in_array($format, self::AVAILABLE_FORMATS, true)) {
            $message = \sprintf(self::ERROR_INVALID_FORMAT, $format, \implode(', ', self::AVAILABLE_FORMATS));

            throw new \InvalidArgumentException($message);
        }
    }
}
