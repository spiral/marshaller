<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Mapper;

use Spiral\Attributes\ReaderInterface;
use Spiral\Marshaller\TypeFactoryInterface;

class AttributeMapperFactory implements MapperFactoryInterface
{
    private ReaderInterface $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function create(\ReflectionClass $class, TypeFactoryInterface $types): MapperInterface
    {
        return new AttributeMapper($class, $types, $this->reader);
    }
}
