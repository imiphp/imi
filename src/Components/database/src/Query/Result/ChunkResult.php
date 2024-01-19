<?php

declare(strict_types=1);

namespace Imi\Db\Query\Result;

use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;

class ChunkResult extends BaseChunkResult
{
    private string $orderBy = '';

    public function __construct(private readonly ?IQuery $query, private readonly int $limit, private readonly string $column, private readonly string $alias, string $orderBy)
    {
        $this->orderBy = strtolower($orderBy);
    }

    /**
     * @return \Traversable<int, IResult>
     */
    public function getIterator(): \Traversable
    {
        $lastId = null;

        do
        {
            $query = clone $this->query;
            if (null !== $lastId)
            {
                $query->where($this->column, 'asc' === $this->orderBy ? '>' : '<', $lastId);
            }
            $query->order($this->column, $this->orderBy);
            $query->limit($this->limit);
            $result = $query->select();

            $resultCount = $result->getRowCount();

            if (0 === $resultCount)
            {
                break;
            }

            yield $result;

            $records = $result->getStatementRecords();

            $lastId = end($records)[$this->alias];
        }
        while ($resultCount === $this->limit);
    }
}
