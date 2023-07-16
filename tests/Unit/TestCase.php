<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Unit;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Spiral\Attributes\AttributeReader;
use Spiral\Marshaller\Mapper\AttributeMapperFactory;
use Spiral\Marshaller\Marshaller;
use Spiral\Marshaller\MarshallerInterface;

abstract class TestCase extends BaseTestCase
{
    protected MarshallerInterface $marshaller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->marshaller = new Marshaller(
            new AttributeMapperFactory(
                new AttributeReader()
            ),
            $this->getTypeMatchers(),
        );
    }

    /**
     * Define custom type matchers for test case.
     */
    protected function getTypeMatchers(): array
    {
        return [];
    }
}
