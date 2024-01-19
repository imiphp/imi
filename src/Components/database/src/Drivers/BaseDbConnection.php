<?php

declare(strict_types=1);

namespace Imi\Db\Drivers;

use Imi\Db\ConnectionCenter\DatabaseDriverConfig;
use Imi\Db\Drivers\Contract\IDbConnection;
use Imi\Util\Traits\THashCode;

abstract class BaseDbConnection implements IDbConnection
{
    use THashCode;

    public function __construct(
        /**
         * 连接配置.
         */
        protected DatabaseDriverConfig $config
    ) {
    }

    /**
     * 获取连接配置.
     */
    public function getConfig(): DatabaseDriverConfig
    {
        return $this->config;
    }

    /**
     * 数据库连接后，执行初始化的 SQL.
     */
    protected function execInitSqls(): void
    {
        if ($sqls = $this->config->initSqls)
        {
            foreach ($sqls as $sql)
            {
                $this->exec($sql);
            }
        }
    }
}
