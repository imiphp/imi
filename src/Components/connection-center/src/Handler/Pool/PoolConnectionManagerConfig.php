<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\Pool;

use Imi\ConnectionCenter\Contract\ConnectionManagerConfig;

/**
 * 连接池连接管理器配置.
 */
class PoolConnectionManagerConfig extends ConnectionManagerConfig
{
    public function __construct(?string $driver = null, ?bool $enableStatistics = null, protected ?PoolConfig $pool = null, array $config = [])
    {
        if (null === $pool)
        {
            $poolConfig = [];
            if (isset($config['pool']))
            {
                if (isset($config['pool']['minResources']))
                {
                    $poolConfig['minResources'] = $config['pool']['minResources'];
                }
                if (isset($config['pool']['maxResources']))
                {
                    $poolConfig['maxResources'] = $config['pool']['maxResources'];
                }
                if (isset($config['pool']['gcInterval']))
                {
                    $poolConfig['gcInterval'] = $config['pool']['gcInterval'];
                }
                if (isset($config['pool']['maxActiveTime']))
                {
                    $poolConfig['maxActiveTime'] = $config['pool']['maxActiveTime'];
                }
                if (isset($config['pool']['waitTimeout']))
                {
                    $poolConfig['waitTimeout'] = $config['pool']['waitTimeout'];
                }
                if (isset($config['pool']['maxUsedTime']))
                {
                    $poolConfig['maxUsedTime'] = $config['pool']['maxUsedTime'];
                }
                if (isset($config['pool']['maxIdleTime']))
                {
                    $poolConfig['maxIdleTime'] = $config['pool']['maxIdleTime'];
                }
                if (isset($config['pool']['heartbeatInterval']))
                {
                    $poolConfig['heartbeatInterval'] = $config['pool']['heartbeatInterval'];
                }
            }
            $this->pool = new PoolConfig(...$poolConfig);
        }
        parent::__construct(driver: $driver, enableStatistics: $enableStatistics, config: $config);
    }

    public function getPool(): PoolConfig
    {
        return $this->pool;
    }
}
