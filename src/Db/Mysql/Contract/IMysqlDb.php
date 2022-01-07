<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Contract;

use Imi\Db\Interfaces\IDb;

interface IMysqlDb extends IDb
{
    /**
     * 检查错误码是否为掉线
     */
    public function checkCodeIsOffline(int $code): bool;
}
