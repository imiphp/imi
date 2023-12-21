<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter;

use Imi\Config;

class AppConnectionCenter extends ConnectionCenter
{
    public function __construct()
    {
        foreach (Config::get('@app.connectionCenter', []) as $name => $connectionManagerConfig)
        {
            if (!isset($connectionManagerConfig['manager']))
            {
                throw new \InvalidArgumentException(sprintf('Config @app.connectionCenter.%s.manager not found', $name));
            }
            if (!isset($connectionManagerConfig['config']))
            {
                throw new \InvalidArgumentException(sprintf('Config @app.connectionCenter.%s.config not found', $name));
            }
            $this->addConnectionManager($name, $connectionManagerConfig['manager'], $connectionManagerConfig['config']);
        }
    }
}
