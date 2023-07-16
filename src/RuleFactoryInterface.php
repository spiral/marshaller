<?php

declare(strict_types=1);

namespace Spiral\Marshaller;

/**
 * Defines ability for {@see TypeFactoryInterface} to produce {@see MarshallingRule}
 * using {@see Type\RuleFactoryInterface}.
 */
interface RuleFactoryInterface extends TypeFactoryInterface
{
    public function makeRule(\ReflectionProperty $property): ?MarshallingRule;
}
