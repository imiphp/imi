<?php

declare(strict_types=1);

namespace Imi\Util;

/**
 * 分页计算类.
 */
class Pagination
{
    /**
     * 当前页码
     *
     * @var int
     */
    private int $page = 0;

    /**
     * 每页显示数量.
     *
     * @var int
     */
    private int $count = 0;

    /**
     * 偏移量.
     *
     * @var int
     */
    private int $limitOffset = 0;

    /**
     * 结束的偏移量（limitOffset + count - 1）.
     *
     * @var int
     */
    private int $limitEndOffset = 0;

    public function __construct(int $page, int $count)
    {
        $this->page = $page;
        $this->count = $count;
        $this->calc();
    }

    /**
     * Get 当前页码
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Set 当前页码
     *
     * @param int $page 当前页码
     *
     * @return self
     */
    public function setPage(int $page): self
    {
        $this->page = $page;

        $this->calc();

        return $this;
    }

    /**
     * Get 每页显示数量.
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Set 每页显示数量.
     *
     * @param int $count 每页显示数量
     *
     * @return self
     */
    public function setCount(int $count): self
    {
        $this->count = $count;

        $this->calc();

        return $this;
    }

    /**
     * 计算.
     *
     * @return void
     */
    private function calc(): void
    {
        $count = $this->count;
        $this->limitOffset = max((int) (($this->page - 1) * $count), 0);
        $this->limitEndOffset = $this->limitOffset + $count - 1;
    }

    /**
     * Get 偏移量.
     *
     * @return int
     */
    public function getLimitOffset(): int
    {
        return $this->limitOffset;
    }

    /**
     * Get 结束的偏移量（limitOffset + count - 1）.
     *
     * @return int
     */
    public function getLimitEndOffset(): int
    {
        return $this->limitEndOffset;
    }

    /**
     * 根据记录数计算总页数.
     *
     * @param int $records
     *
     * @return int
     */
    public function calcPageCount(int $records): int
    {
        $count = $this->count;
        if (0 === $records % $count)
        {
            return $records / $count;
        }
        else
        {
            return ((int) ($records / $count)) + 1;
        }
    }
}
