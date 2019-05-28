<?php
namespace Imi\Util;

/**
 * 分页计算类
 */
class Pagination
{
    /**
     * 当前页码
     *
     * @var int
     */
    private $page;

    /**
     * 每页显示数量
     *
     * @var int
     */
    private $count;

    /**
     * 偏移量
     *
     * @var int
     */
    private $limitOffset;

    /**
     * 结束的偏移量（limitOffset + count）
     *
     * @var int
     */
    private $limitEndOffset;

    public function __construct($page, $count)
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
    public function getPage()
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
    public function setPage(int $page)
    {
        $this->page = $page;

        $this->calc();
        return $this;
    }

    /**
     * Get 每页显示数量
     *
     * @return int
     */ 
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set 每页显示数量
     *
     * @param int $count 每页显示数量
     *
     * @return self
     */ 
    public function setCount(int $count)
    {
        $this->count = $count;

        $this->calc();
        return $this;
    }

    /**
     * 计算
     *
     * @return void
     */
    private function calc()
    {
        $this->limitOffset = max((int)(($this->page - 1) * $this->count), 0);
        $this->limitEndOffset = $this->limitOffset + $this->count;
    }

    /**
     * Get 偏移量
     *
     * @return int
     */ 
    public function getLimitOffset()
    {
        return $this->limitOffset;
    }

    /**
     * Get 结束的偏移量（limitOffset + count）
     *
     * @return int
     */ 
    public function getLimitEndOffset()
    {
        return $this->limitEndOffset;
    }
}