<?php

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IOrder;
use Imi\Db\Query\Traits\TKeyword;
use Imi\Db\Query\Traits\TRaw;

class Order implements IOrder
{
    use TRaw;
    use TKeyword;

    /**
     * 字段名.
     *
     * @var string|null
     */
    protected $fieldName;

    /**
     * 排序方向：asc/desc.
     *
     * @var string
     */
    protected $direction;

    public function __construct(?string $fieldName = null, string $direction = 'asc')
    {
        $this->fieldName = $fieldName;
        $this->direction = $direction;
    }

    /**
     * 获取字段名.
     *
     * @return string|null
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * 获取排序方向：asc/desc.
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * 设置字段名.
     *
     * @param string|null $fieldName
     *
     * @return void
     */
    public function setFieldName(?string $fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * 设置排序方向：asc/desc.
     *
     * @param string $direction
     *
     * @return void
     */
    public function setDirection(string $direction)
    {
        $this->direction = $direction;
    }

    public function __toString()
    {
        if ($this->isRaw)
        {
            return $this->rawSQL;
        }

        return $this->parseKeyword($this->fieldName) . ' ' . $this->direction;
    }

    /**
     * 获取绑定的数据们.
     *
     * @return array
     */
    public function getBinds()
    {
        return [];
    }
}
