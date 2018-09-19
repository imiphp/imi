<?php
namespace Imi\Db\Query\Traits;

trait TRaw
{
    /**
     * 是否使用原生语句
     * @var boolean
     */
    protected $isRaw;

    /**
     * 原生语句
     * @var string
     */
    protected $rawSQL;

    /**
     * 获取是否使用原生语句
     * @return boolean
     */
    public function isRaw(): bool
    {
        return $this->isRaw;
    }

    /**
     * 设置是否使用原生语句
     * @param boolean $isRaw
     * @return void
     */
    public function useRaw($isRaw = true)
    {
        $this->isRaw = $isRaw;
    }

    /**
     * 设置原生语句
     * @param string $rawSQL
     * @return void
     */
    public function setRawSQL(string $rawSQL)
    {
        $this->rawSQL = $rawSQL;
    }
}