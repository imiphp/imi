<?php

declare(strict_types=1);

namespace Imi\Db\Query\Result;

abstract class BaseChunkResult implements \IteratorAggregate
{
    abstract public function getIterator(): \Traversable;

    public function each(): \Generator
    {
        foreach ($this as $result)
        {
            yield from $result->getArray();
        }
    }
}
