<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IPaginateResult extends IResult, \JsonSerializable
{
    /**
     * 获取数组数据.
     */
    public function getList(): array;

    /**
     * 获取记录总数.
     */
    public function getTotal(): ?int;

    /**
     * 获取查询几条记录.
     */
    public function getLimit(): int;

    /**
     * 获取总页数.
     */
    public function getPageCount(): ?int;

    /**
     * 将当前对象作为数组返回.
     */
    public function toArray(): array;
}
