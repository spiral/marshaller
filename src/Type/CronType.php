<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

class CronType extends Type
{
    /**
     * @var string
     */
    private const ERROR_INVALID_TYPE =
        'Passed value must be a type of ' .
        'cron-like string or cron expression, but %s given';

    public function parse($value, $current)
    {
        if ($value === '') {
            // by default empty cron string = no cron
            return null;
        }

        if (\is_string($value)) {
            return $value;
        }

        throw new \InvalidArgumentException(\sprintf(self::ERROR_INVALID_TYPE, \get_debug_type($value)));
    }

    public function serialize($value)
    {
        if (\is_string($value) || $value instanceof \Stringable) {
            return (string)$value;
        }

        throw new \InvalidArgumentException(\sprintf(self::ERROR_INVALID_TYPE, \get_debug_type($value)));
    }
}
