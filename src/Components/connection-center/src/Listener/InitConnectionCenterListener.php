<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\ConnectionCenter\Facade\ConnectionCenter;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Process\Event\ProcessEvents;
use Imi\Server\Event\ServerEvents;

#[Listener(eventName: ProcessEvents::PROCESS_BEGIN)]
#[Listener(eventName: ServerEvents::WORKER_START)]
class InitConnectionCenterListener implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
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
            ConnectionCenter::addConnectionManager($name, $connectionManagerConfig['manager'], $connectionManagerConfig['config']);
        }
    }
}
