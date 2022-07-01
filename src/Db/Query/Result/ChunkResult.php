<?php

declare(strict_types=1);

namespace Imi\Db\Query\Result;

use function end;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;

class ChunkResult extends ChunkResultAbstract
{
    private IQuery $query;
    private int    $limit;
    private string $column;
    private string $alias;

    public function __construct(IQuery $query, int $limit, string $column, string $alias)
    {
        $this->query = $query;
        $this->limit = $limit;
        $this->column = $column;
        $this->alias = $alias;
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
                $query->where($this->column, '>', $lastId);
            }
            $query->order($this->column, 'asc');
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
