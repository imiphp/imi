<?php
namespace Imi\Db\Query\Where;

abstract class BaseWhere
{
    public function __toString()
    {
        throw new \RuntimeException(sprintf('%s object can not be used as string', __CLASS__));
    }

}