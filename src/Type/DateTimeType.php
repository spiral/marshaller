<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use JetBrains\PhpStorm\Pure;
use Spiral\Marshaller\MarshallerInterface;
use Spiral\Marshaller\Internal\Support\DateTime;
use Spiral\Marshaller\Internal\Support\Inheritance;

class DateTimeType extends Type implements DetectableTypeInterface
{
    private string $format;

    #[Pure]
    public function __construct(MarshallerInterface $marshaller, string $format = \DateTimeInterface::RFC3339)
    {
        $this->format = $format;

        parent::__construct($marshaller);
    }

    public static function match(\ReflectionNamedType $type): bool
    {
        return !$type->isBuiltin() && Inheritance::implements($type->getName(), \DateTimeInterface::class);
    }

    public function parse($value, $current): \DateTimeInterface
    {
        return DateTime::parse($value);
    }

    public function serialize($value): string
    {
        return DateTime::parse($value)
            ->format($this->format);
    }
}
