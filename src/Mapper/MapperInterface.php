<?php

declare(strict_types=1);

namespace Spiral\Marshaller\Mapper;

/**
 * @psalm-type Getter = \Closure(): mixed
 * @psalm-type Setter = \Closure(mixed): void
 */
interface MapperInterface
{
    public function isCopyOnWrite(): bool;

    /**
     * @return iterable<string, Getter>
     */
    public function getGetters(): iterable;

    /**
     * @return iterable<string, Setter>
     */
    public function getSetters(): iterable;
}
