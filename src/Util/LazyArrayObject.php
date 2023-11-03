<?php

declare(strict_types=1);

namespace Imi\Util;

/**
 * 同时可以作为数组和对象访问的类.
 */
class LazyArrayObject extends \ArrayObject implements \JsonSerializable
{
    public function __construct(mixed $input = [], int $flags = self::ARRAY_AS_PROPS, string $iteratorClass = \ArrayIterator::class)
    {
        parent::__construct($input, $flags, $iteratorClass);
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }

    /**
     * 将当前对象作为数组返回.
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }
}
