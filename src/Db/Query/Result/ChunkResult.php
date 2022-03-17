<?php

declare(strict_types=1);

namespace Imi\Db\Query\Result;

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
     * @return \Traversable<int, IResult>|IResult[]
     */
    public function getIterator()
    {
        return $this->chunkIterator();
    }

    private function chunkIterator(): \Generator
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

            // todo 返回 result 对象还是结果数组？
            yield $result;

            // todo 理论上如果是模型查询应该通过模型获取吧，但 getArray 方法转换模型没缓存？
            $records = $result->getStatementRecords();

            $lastId = $records[array_key_last($records)][$this->alias];
        }
        while ($resultCount === $this->limit);
    }
}
