<?php

declare(strict_types=1);

namespace Imi\Db\Drivers;

use Imi\Db\Interfaces\IDb;
use Imi\Util\Traits\THashCode;

abstract class Base implements IDb
{
    use THashCode;

    /**
     * 连接配置.
     */
    protected array $option;

    public function __construct(array $option = [])
    {
        $this->option = $option;
    }

    /**
     * 获取连接配置.
     */
    public function getOption(): array
    {
        return $this->option;
    }

    /**
     * 数据库连接后，执行初始化的 SQL.
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
