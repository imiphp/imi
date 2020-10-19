<?php

namespace Imi\Db\Query\Interfaces;

interface IPaginateResult extends IResult, \JsonSerializable
{
    /**
     * 获取数组数据.
     *
     * @return void
     */
    public function getList();

    /**
     * 获取记录总数.
     *
     * @return int
     */
    public function getTotal();

    /**
     * 获取查询几条记录.
     *
     * @return int
     */
    public function getLimit();

    /**
     * 获取总页数.
     *
     * @return int
     */
    public function getPageCount();

    /**
     * 将当前对象作为数组返回.
     *
     * @return array
     */
    public function toArray(): array;
}
