<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use Spiral\Marshaller\MarshallerInterface;

interface TypeInterface
{
    public function __construct(MarshallerInterface $marshaller);

    /**
     * @param mixed $value
     * @param mixed $current
     * @return mixed
     */
    public function parse($value, $current);

    /**
     * @param mixed $value
     * @return mixed
     */
    public function serialize($value);
}
