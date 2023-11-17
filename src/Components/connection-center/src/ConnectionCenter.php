<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter;

use Imi\App;
use Imi\ConnectionCenter\Contract\IConnection;
use Imi\ConnectionCenter\Contract\IConnectionManager;

class ConnectionCenter
{
    /**
     * @var IConnectionManager[]
     */
    protected array $connectionManagers = [];

    public function addConnectionManager(string $name, string $connectionManagerClass, mixed $config): IConnectionManager
    {
        if (isset($this->connectionManagers[$name]))
        {
            throw new \RuntimeException(sprintf('Connection manager %s already exists', $name));
        }

        return $this->connectionManagers[$name] = App::newInstance($connectionManagerClass, $config);
    }

    public function removeConnectionManager(string $name): void
    {
        $connectionManager = $this->getConnectionManager($name);
        $connectionManager->close();
        unset($this->connectionManagers[$name]);
    }

    public function hasConnectionManager(string $name): bool
    {
        return isset($this->connectionManagers[$name]);
    }

    public function getConnectionManager(string $name): IConnectionManager
    {
        if (isset($this->connectionManagers[$name]))
        {
            return $this->connectionManagers[$name];
        }
        else
        {
            throw new \RuntimeException(sprintf('Connection manager %s does not exists', $name));
        }
    }

    public function getConnection(string $name): IConnection
    {
        return $this->getConnectionManager($name)->getConnection();
    }
}
