<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use Spiral\Marshaller\MarshallerInterface;
use Spiral\Marshaller\MarshallingRule;

class NullableType extends Type
{
    private ?TypeInterface $type = null;

    /**
     * @throws \ReflectionException
     */
    public function __construct(MarshallerInterface $marshaller, MarshallingRule|string $typeOrClass = null)
    {
        if ($typeOrClass !== null) {
            $this->type = $this->ofType($marshaller, $typeOrClass);
        }

        parent::__construct($marshaller);
    }

    public function parse(mixed $value, mixed $current): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($this->type) {
            return $this->type->parse($value, $current);
        }

        return $value;
    }

    public function serialize(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($this->type) {
            return $this->type->serialize($value);
        }

        return $value;
    }
}
