<?php

declare(strict_types=1);

namespace Spiral\Marshaller;

use Spiral\Marshaller\Type\ArrayType;
use Spiral\Marshaller\Type\DateTimeType;
use Spiral\Marshaller\Type\DetectableTypeInterface;
use Spiral\Marshaller\Type\EnumType;
use Spiral\Marshaller\Type\ObjectType;
use Spiral\Marshaller\Type\RuleFactoryInterface as TypeRuleFactoryInterface;
use Spiral\Marshaller\Type\TypeInterface;
use Spiral\Marshaller\Type\UuidType;

/**
 * @psalm-type CallableTypeMatcher = \Closure(\ReflectionNamedType): ?string
 * @psalm-type CallableTypeDtoMatcher = \Closure(\ReflectionProperty): ?MarshallingRule
 */
class TypeFactory implements RuleFactoryInterface
{
    /**
     * @var string
     */
    private const ERROR_INVALID_TYPE = 'Mapping type must implement %s, but %s given';

    /**
     * @var array<CallableTypeMatcher|DetectableTypeInterface>
     */
    private array $matchers = [];

    /**
     * @var array<TypeRuleFactoryInterface|class-string<TypeRuleFactoryInterface>>
     */
    private array $typeDtoMatchers = [];

    private MarshallerInterface $marshaller;

    /**
     * @param array<CallableTypeMatcher|DetectableTypeInterface|TypeRuleFactoryInterface> $matchers
     */
    public function __construct(MarshallerInterface $marshaller, array $matchers)
    {
        $this->marshaller = $marshaller;

        $this->createMatchers($matchers);
        $this->createMatchers($this->getDefaultMatchers());
    }

    public function create(string $type, array $args): ?TypeInterface
    {
        if (!\is_subclass_of($type, TypeInterface::class)) {
            throw new \InvalidArgumentException(\sprintf(self::ERROR_INVALID_TYPE, TypeInterface::class, $type));
        }

        return new $type($this->marshaller, ...$args);
    }

    public function detect(?\ReflectionType $type): ?string
    {
        /**
         * - Union types ({@see \ReflectionUnionType}) cannot be uniquely determined.
         * - The {@see null} type is an alias of "mixed" type.
         */
        if (!$type instanceof \ReflectionNamedType) {
            return null;
        }

        foreach ($this->matchers as $matcher) {
            if ($result = $matcher($type)) {
                return $result;
            }
        }

        return null;
    }

    public function makeRule(\ReflectionProperty $property): ?MarshallingRule
    {
        foreach ($this->typeDtoMatchers as $matcher) {
            $result = $matcher::makeRule($property);
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param iterable<CallableTypeMatcher|DetectableTypeInterface|TypeRuleFactoryInterface> $matchers
     */
    private function createMatchers(iterable $matchers): void
    {
        foreach ($matchers as $matcher) {
            if ($matcher instanceof \Closure) {
                $this->matchers[] = $matcher;
                continue;
            }

            if (\is_subclass_of($matcher, TypeRuleFactoryInterface::class, true)) {
                $this->typeDtoMatchers[] = $matcher;
            }

            if (\is_subclass_of($matcher, DetectableTypeInterface::class, true)) {
                $this->matchers[] = static fn (\ReflectionNamedType $type): ?string => $matcher::match($type)
                    ? $matcher
                    : null;
            }
        }
    }

    /**
     * @return iterable<class-string<DetectableTypeInterface>>
     */
    private function getDefaultMatchers(): iterable
    {
        yield EnumType::class;
        yield DateTimeType::class;
        yield UuidType::class;
        yield ArrayType::class;
        yield ObjectType::class;
    }
}
