<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use Spiral\Marshaller\MarshallerInterface;

interface TypeInterface
{
    public function __construct(MarshallerInterface $marshaller);

    public function parse(mixed $value, mixed $current): mixed;

    public function serialize(mixed $value): mixed;
}
