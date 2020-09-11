<?php

namespace Imi\Db\Query\Interfaces;

interface IBase
{
    /**
     * 获取是否使用原生语句.
     *
     * @return bool
     */
    public function isRaw(): bool;

    /**
     * 设置是否使用原生语句.
     *
     * @param bool $isRaw
     *
     * @return void
     */
    public function useRaw($isRaw = true);

    /**
     * 设置原生语句.
     *
     * @param string $rawSQL
     *
     * @return void
     */
    public function setRawSQL(string $rawSQL);

    public function __toString();

    /**
     * 获取绑定的数据们.
     *
     * @return array
     */
    public function getBinds();
}
