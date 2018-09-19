<?php
namespace Imi\Db\Query\Interfaces;

interface IBaseWhere extends IBase
{
    public function toStringWithoutLogic();
}