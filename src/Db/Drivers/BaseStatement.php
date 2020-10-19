<?php

namespace Imi\Db\Drivers;

use Imi\Db\Interfaces\IStatement;
use Imi\Db\Statement\StatementManager;

abstract class BaseStatement implements IStatement
{
    /**
     * 关闭.
     *
     * @return void
     */
    public function close()
    {
        StatementManager::remove($this);
    }
}
