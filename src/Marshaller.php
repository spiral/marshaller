<?php

declare(strict_types=1);

namespace Spiral\Marshaller;

use Spiral\Marshaller\Exception\InvalidArgumentException;
use Spiral\Marshaller\Mapper\MapperFactoryInterface;
use Spiral\Marshaller\Mapper\MapperInterface;

/**
 * @psalm-import-type CallableTypeMatcher from TypeFactory
 */
class Marshaller implements MarshallerInterface
{
    /**
     * @var array<string, MapperInterface>
     */
    private array $mappers = [];

    private TypeFactory $type;
    private MapperFactoryInterface $mapper;

    /**
     * @param MapperFactoryInterface $mapper
     * @param array<CallableTypeMatcher> $matchers
     */
    public function __construct(MapperFactoryInterface $mapper, array $matchers = [])
    {
        $this->mapper = $mapper;
        $this->type = new TypeFactory($this, $matchers);
    }

    /**
     * @throws \ReflectionException
     */
    public function marshal(object $from): array
    {
        $mapper = $this->getMapper(\get_class($from));

        $result = [];

        foreach ($mapper->getGetters() as $field => $getter) {
            $result[$field] = $getter->call($from);
        }

        return $result;
    }

    /**
     * @throws \ReflectionException
     */
    public function unmarshal(array $from, object $to): object
    {
        $class = $to::class;

        if ($class === \stdClass::class) {
            foreach ($from as $key => $value) {
                $to->{$key} = $value;
            }

            return $to;
        }

        $mapper = $this->getMapper($class);
        $result = $mapper->isCopyOnWrite() ? clone $to : $to;

        foreach ($mapper->getSetters() as $field => $setter) {
            if (!\array_key_exists($field, $from)) {
                continue;
            }

            try {
                $setter->call($result, $from[$field] ?? null);
            } catch (\Throwable $e) {
                throw new InvalidArgumentException(
                    \sprintf('Unable to unmarshal field `%s` of class %s', $field, $to::class),
                    previous: $e
                );
            }
        }

        return $result;
    }

    /**
     * @param class-string $class
     * @throws \ReflectionException
     */
    private function factory(string $class): MapperInterface
    {
        $reflection = new \ReflectionClass($class);

        return $this->mapper->create($reflection, $this->type);
    }

    /**
     * @param class-string $class
     * @throws \ReflectionException
     */
    private function getMapper(string $class): MapperInterface
    {
        return $this->mappers[$class] ??= $this->factory($class);
    }
}
