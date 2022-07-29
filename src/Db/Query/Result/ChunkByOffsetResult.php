<?php

declare(strict_types=1);

namespace Imi\Db\Query\Result;

use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;

class ChunkByOffsetResult extends BaseChunkResult
{
    private ?IQuery $query = null;

    private int $limit = 0;

    public function __construct(IQuery $query, int $limit)
    {
        $this->query = $query;
        $this->limit = $limit;
    }

    /**
     * @return \Traversable|\Generator|iterable<int, IResult>
     */
    public function getIterator()
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
