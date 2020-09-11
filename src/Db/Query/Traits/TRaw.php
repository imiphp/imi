<?php

namespace Imi\Db\Query\Traits;

trait TRaw
{
    /**
     * 是否使用原生语句.
     *
     * @var bool
     */
    protected $isRaw;

    /**
     * 原生语句.
     *
     * @var string
     */
    protected $rawSQL;

    /**
     * 获取是否使用原生语句.
     *
     * @return bool
     */
    public function isRaw(): bool
    {
        return $this->isRaw;
    }

    /**
     * 设置是否使用原生语句.
     *
     * @param bool $isRaw
     *
     * @return void
     */
    public function useRaw($isRaw = true)
    {
        $this->isRaw = $isRaw;
    }

    /**
     * 设置原生语句.
     *
     * @param string $rawSQL
     *
     * @return void
     */
    public function setRawSQL(string $rawSQL)
    {
        $this->rawSQL = $rawSQL;
    }
}
