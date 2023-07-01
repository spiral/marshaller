<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Type;

use Spiral\Marshaller\MarshallingRule;

/**
 * The type can detect the property type information from its reflection.
 */
interface RuleFactoryInterface extends TypeInterface
{
    /**
     * Make a marshalling rule for the given property.
     */
    public static function makeRule(\ReflectionProperty $property): ?MarshallingRule;
}
