<?php

declare(strict_types=1);

namespace Imi\Db\Query\Result;

use function end;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;
use function strtolower;

class ChunkResult extends BaseChunkResult
{
    private ?IQuery $query = null;

    private int $limit = 0;

    private string $column = '';

    private string $alias = '';

    private string $orderBy = '';

    public function __construct(IQuery $query, int $limit, string $column, string $alias, string $orderBy)
    {
        $this->query = $query;
        $this->limit = $limit;
        $this->column = $column;
        $this->alias = $alias;
        $this->orderBy = strtolower($orderBy);
    }

    /**
     * @return \Traversable|\Generator|iterable<int, IResult>
     */
    public function getIterator()
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
