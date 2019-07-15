<?php
namespace Imi\Db\Query\Interfaces;

interface IBaseWhere extends IBase
{
    /**
     * 获取无逻辑的字符串
     *
     * @param IQuery $query
     * @return string
     */
    public function toStringWithoutLogic(IQuery $query);

}