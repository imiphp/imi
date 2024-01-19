<?php

declare(strict_types=1);

namespace Imi\Db\ConnectionCenter;

use Imi\App;
use Imi\ConnectionCenter\Contract\AbstractConnectionDriver;
use Imi\ConnectionCenter\Contract\IConnectionConfig;
use Imi\Db\Drivers\Contract\IDbConnection;

class DatabaseDriver extends AbstractConnectionDriver
{
    protected ?string $databaseConnectionDriver = null;

    public static function createConnectionConfig(string|array $config): IConnectionConfig
    {
        return DatabaseDriverConfig::create($config);
    }

    protected function createInstanceByConfig(IConnectionConfig $config): object
    {
        return App::newInstance($this->getDatabaseConnectionDriver(), $config);
    }

    /**
     * @param IDbConnection $instance
     */
    public function connect(object $instance): object
    {
        $instance->open();

        return $instance;
    }

    /**
     * @param IDbConnection $instance
     */
    public function close(object $instance): void
    {
        $instance->close();
    }

    /**
     * @param IDbConnection $instance
     */
    public function reset(object $instance): void
    {
    }

    /**
     * @param IDbConnection $instance
     */
    public function checkAvailable(object $instance): bool
    {
        return $instance->ping();
    }

    /**
     * @param IDbConnection $instance
     */
    public function ping(object $instance): bool
    {
        return $instance->ping();
    }

    public function getDatabaseConnectionDriver(): string
    {
        if (null === $this->databaseConnectionDriver)
        {
            $config = $this->connectionManagerConfig->getConfig();
            if (!isset($config['dbDriver']))
            {
                throw new \InvalidArgumentException('To use the Connection Center DatabaseDriver, "dbDriver" must be configured.');
            }

            return $this->databaseConnectionDriver = $config['dbDriver'];
        }

        return $this->databaseConnectionDriver;
    }
}
