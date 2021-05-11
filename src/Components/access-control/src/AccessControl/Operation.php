<?php

namespace Imi\AC\AccessControl;

use Imi\App;

abstract class Operation
{
    /**
     * 创建操作权限.
     *
     * @param string      $name
     * @param string|null $code
     * @param int         $parentId
     * @param int         $index
     * @param string      $description
     *
     * @return \Imi\AC\Model\Operation
     */
    public static function create($name, $code = null, $parentId = 0, $index = 0, $description = '')
    {
        return App::getBean('ACOperationService')->create($name, $code, $parentId, $index, $description);
    }
}
