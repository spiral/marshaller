<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Mapper;

use Spiral\Marshaller\TypeFactoryInterface;

interface MapperFactoryInterface
{
    public function create(\ReflectionClass $class, TypeFactoryInterface $types): MapperInterface;
}
