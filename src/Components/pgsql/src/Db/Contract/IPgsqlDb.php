<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Contract;

use Imi\Db\Interfaces\IDb;

interface IPgsqlDb extends IDb
{
    /**
     * 检查错误码是否为掉线
     */
    public function checkCodeIsOffline(?string $code): bool;
}
