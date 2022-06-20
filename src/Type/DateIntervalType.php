<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use Carbon\CarbonInterval;
use Spiral\Marshaller\MarshallerInterface;
use Spiral\Marshaller\Internal\Support\DateInterval;
use Spiral\Marshaller\Internal\Support\Inheritance;

/**
 * @psalm-import-type DateIntervalFormat from DateInterval
 */
class DateIntervalType extends Type implements DetectableTypeInterface
{
    private string $format;

    public function __construct(MarshallerInterface $marshaller, string $format = DateInterval::FORMAT_NANOSECONDS)
    {
        $this->format = $format;

        parent::__construct($marshaller);
    }

    public static function match(\ReflectionNamedType $type): bool
    {
        return !$type->isBuiltin() && Inheritance::extends($type->getName(), \DateInterval::class);
    }

    public function serialize($value): int
    {
        $method = 'total' . \ucfirst($this->format);

        if ($this->format === DateInterval::FORMAT_NANOSECONDS) {
            return (int)(DateInterval::parse($value, $this->format)->totalMicroseconds * 1000);
        }

        return (int)(DateInterval::parse($value, $this->format)->$method);
    }

    public function parse($value, $current): CarbonInterval
    {
        return DateInterval::parse($value, $this->format);
    }
}
