<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Meta;

use JetBrains\PhpStorm\ExpectedValues;
use Spiral\Attributes\NamedArgumentConstructorAttribute;

/**
 * @psalm-type ExportScope = Scope::VISIBILITY_*
 *
 * @Annotation
 * @Target({ "CLASS" })
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Scope implements NamedArgumentConstructorAttribute
{
    /**
     * @var int
     */
    public const VISIBILITY_PRIVATE = \ReflectionProperty::IS_PRIVATE;

    /**
     * @var int
     */
    public const VISIBILITY_PROTECTED = \ReflectionProperty::IS_PROTECTED;

    /**
     * @var int
     */
    public const VISIBILITY_PUBLIC = \ReflectionProperty::IS_PUBLIC;

    /**
     * @var int
     */
    public const VISIBILITY_ALL = self::VISIBILITY_PRIVATE
                                | self::VISIBILITY_PROTECTED
                                | self::VISIBILITY_PUBLIC;

    /**
     * @var ExportScope
     */
    #[ExpectedValues(valuesFromClass: Scope::class)]
    public int $properties;

    public bool $copyOnWrite;

    public function __construct(
        int $properties = self::VISIBILITY_PUBLIC,
        bool $copyOnWrite = false
    ) {
        $this->properties = $properties;
        $this->copyOnWrite = $copyOnWrite;
    }
}