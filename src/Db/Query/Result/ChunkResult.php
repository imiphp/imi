<?php

declare(strict_types=1);

namespace Imi\Db\Query\Result;

use function end;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;

class ChunkResult implements \IteratorAggregate
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
     * @return \Traversable<int, IResult>|\Generator|iterable<int, IResult>
     */
    #[\ReturnTypeWillChange]
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

            // todo 如果是模型查询应该通过模型获取，但 getArray 方法转换模型没缓存，暂时先从原始数据里获取
            $lastId = end($records)[$this->alias];
        }
        while ($resultCount === $this->limit);
    }
}
