<?php

declare(strict_types=1);

namespace Spiral\Marshaller;

interface MarshallerInterface
{
    /**
     * @template T of object
     * @param T $from
     */
    public function marshal(object $from): array;

    /**
     * @template T of object
     * @param T $to
     * @return T
     */
    public function unmarshal(array $from, object $to): object;
}
