<?php
namespace Imi\Db\Query\Where;

abstract class BaseWhere
{
    /**
     * 逻辑运算符
     * @var string
     */
    protected $logicalOperator;

    public function __toString()
    {
        trigger_error(sprintf('%s object can not be used as string', get_called_class()), E_USER_ERROR);
        return '';
    }

}