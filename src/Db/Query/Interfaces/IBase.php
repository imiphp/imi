<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IBase
{
    /**
     * 获取是否使用原生语句.
     */
    public function isRaw(): bool;

    /**
     * 设置是否使用原生语句.
     */
    public function useRaw(bool $isRaw = true): void;

    /**
     * 设置原生语句.
     */
    public function setRawSQL(string $rawSQL): void;

    public function toString(IQuery $query): string;

    /**
     * 获取绑定的数据们.
     */
    public function getBinds(): array;
}
