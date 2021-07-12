<?php

declare(strict_types=1);

namespace Imi\AC\AccessControl;

use Imi\App;

abstract class Operation
{
    /**
     * 创建操作权限.
     */
    public static function create(string $name, ?string $code = null, int $parentId = 0, int $index = 0, string $description = ''): \Imi\AC\Model\Operation
    {
        return App::getBean('ACOperationService')->create($name, $code, $parentId, $index, $description);
    }
}
