<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Contract;

use Imi\Db\Interfaces\IDb;

interface IPgsqlDb extends IDb
{
    /**
     * 检查错误码是否为掉线
     *
     * @param string|null $code
     */
    public function checkCodeIsOffline($code): bool;
}
