<?php

namespace Imi\Db\Drivers;

use Imi\Db\Interfaces\IDb;
use Imi\Util\Traits\THashCode;

abstract class Base implements IDb
{
    use THashCode;

    /**
     * 连接配置.
     *
     * @var array
     */
    protected $option;

    public function __construct(array $option = [])
    {
        $this->option = $option;
    }

    /**
     * 数据库连接后，执行初始化的 SQL.
     *
     * @return void
     */
    protected function execInitSqls(): void
    {
        $sqls = $this->option['initSqls'] ?? [];
        if ($sqls)
        {
            foreach ($sqls as $sql)
            {
                $this->exec($sql);
            }
        }
    }
}
