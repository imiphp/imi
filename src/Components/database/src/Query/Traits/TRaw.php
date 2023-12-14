<?php

declare(strict_types=1);

namespace Imi\Db\Query\Traits;

trait TRaw
{
    /**
     * 是否使用原生语句.
     */
    protected bool $isRaw = false;

    /**
     * 原生语句.
     */
    protected string $rawSQL = '';

    /**
     * 绑定的数据们.
     */
    protected array $binds = [];

    /**
     * 获取是否使用原生语句.
     */
    public function isRaw(): bool
    {
        return $this->isRaw;
    }

    /**
     * 设置是否使用原生语句.
     */
    public function useRaw(bool $isRaw = true): void
    {
        $this->isRaw = $isRaw;
    }

    /**
     * 设置原生语句.
     */
    public function setRawSQL(string $rawSQL, array $binds = []): void
    {
        $this->rawSQL = $rawSQL;
        $this->binds = $binds;
    }

    /**
     * {@inheritDoc}
     */
    public function getBinds(): array
    {
        return $this->binds;
    }
}
