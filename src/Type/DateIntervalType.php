<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use Carbon\CarbonInterval;
use Spiral\Marshaller\MarshallerInterface;
use Spiral\Marshaller\MarshallingRule;
use Spiral\Marshaller\Support\DateInterval;
use Spiral\Marshaller\Support\Inheritance;

/**
 * @psalm-import-type DateIntervalFormat from DateInterval
 */
class DateIntervalType extends Type implements DetectableTypeInterface, RuleFactoryInterface
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

    public static function makeRule(\ReflectionProperty $property): ?MarshallingRule
    {
        $type = $property->getType();

        if (!$type instanceof \ReflectionNamedType || !\is_subclass_of($type->getName(), \DateInterval::class)) {
            return null;
        }

        return $type->allowsNull()
            ? new MarshallingRule($property->getName(), NullableType::class, self::class)
            : new MarshallingRule($property->getName(), self::class);
    }

    /**
     * @psalm-assert \DateInterval $value
     */
    public function serialize(mixed $value): int
    {
        $method = 'total' . \ucfirst($this->format);

        if ($this->format === DateInterval::FORMAT_NANOSECONDS) {
            return (int)(DateInterval::parse($value, $this->format)->totalMicroseconds * 1000);
        }

        return (int)(DateInterval::parse($value, $this->format)->$method);
    }

    public function parse(mixed $value, mixed $current): CarbonInterval
    {
        return DateInterval::parse($value, $this->format);
    }
}
