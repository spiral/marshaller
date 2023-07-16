<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Tests\Fixture\Unit\Marshaller;

final class A
{
    public string $x;
    public ?B $b;

    public function __construct(string $x, ?B $b = null)
    {
        $this->x = $x;
        $this->b = $b;
    }
}
