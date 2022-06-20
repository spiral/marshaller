<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use Spiral\Marshaller\MarshallerInterface;

class NullableType extends Type
{
    private ?TypeInterface $type = null;

    /**
     * @throws \ReflectionException
     */
    public function __construct(MarshallerInterface $marshaller, string $typeOrClass = null)
    {
        if ($typeOrClass !== null) {
            $this->type = $this->ofType($marshaller, $typeOrClass);
        }

        parent::__construct($marshaller);
    }

    /**
     * @param mixed $value
     * @param mixed $current
     * @return mixed
     */
    public function parse($value, $current)
    {
        if ($value === null) {
            return null;
        }

        if ($this->type) {
            return $this->type->parse($value, $current);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function serialize($value)
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
