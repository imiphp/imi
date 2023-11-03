<?php

declare(strict_types=1);

namespace Imi\Db\Query\Result;

use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;

class ChunkByOffsetResult extends BaseChunkResult
{
    public function __construct(private readonly ?IQuery $query, private readonly int $limit)
    {
    }

    /**
     * @return \Traversable<int, IResult>
     */
    public function getIterator(): \Traversable
    {
        $offset = 0;

        do
        {
            $result = (clone $this->query)
                ->offset($offset)
                ->limit($this->limit)
                ->select();

            $resultCount = $result->getRowCount();

            if (0 === $resultCount)
            {
                break;
            }

            yield $result;

            $offset += $this->limit;
        }
        while ($resultCount === $this->limit);
    }
}
