<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use DateTimeInterface;
use JetBrains\PhpStorm\Pure;
use Spiral\Marshaller\MarshallerInterface;
use Spiral\Marshaller\MarshallingRule;
use Spiral\Marshaller\Support\DateTime;
use Spiral\Marshaller\Support\Inheritance;

class DateTimeType extends Type implements DetectableTypeInterface, RuleFactoryInterface
{
    private string $format;

    /**
     * @var class-string<DateTimeInterface>
     */
    private string $class;

    /**
     * @param class-string<DateTimeInterface> $class
     */
    #[Pure]
    public function __construct(
        MarshallerInterface $marshaller,
        string $class = DateTimeInterface::class,
        string $format = \DateTimeInterface::RFC3339,
    ) {
        $this->format = $format;

        parent::__construct($marshaller);
        $this->class = $class;
    }

    public static function match(\ReflectionNamedType $type): bool
    {
        return !$type->isBuiltin() && Inheritance::implements($type->getName(), \DateTimeInterface::class);
    }

    public static function makeRule(\ReflectionProperty $property): ?MarshallingRule
    {
        $type = $property->getType();

        if (!$type instanceof \ReflectionNamedType || !\is_subclass_of($type->getName(), \DateTimeInterface::class)) {
            return null;
        }

        return $type->allowsNull()
            ? new MarshallingRule(
                $property->getName(),
                NullableType::class,
                new MarshallingRule(type: self::class, of: $type->getName()),
            )
            : new MarshallingRule($property->getName(), self::class, $type->getName());
    }

    /**
     * @psalm-assert string $value
     */
    public function parse(mixed $value, mixed $current): \DateTimeInterface
    {
        return DateTime::parse($value, class: $this->class);
    }

    /**
     * @psalm-assert DateTimeInterface $value
     */
    public function serialize(mixed $value): string
    {
        return DateTime::parse($value)
            ->format($this->format);
    }
}
