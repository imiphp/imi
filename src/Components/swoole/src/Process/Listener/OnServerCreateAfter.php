<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Server\Event\ServerEvents;
use Imi\Server\ServerManager;
use Imi\Swoole\Process\ProcessManager;
use Imi\Swoole\Server\Contract\ISwooleServer;

#[Listener(eventName: ServerEvents::AFTER_CREATE_SERVERS, priority: -19940311, one: true)]
class OnServerCreateAfter implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        $servers = ServerManager::getServers();
        $server = reset($servers);
        if (!$server instanceof ISwooleServer)
        {
            return;
        }
        // @phpstan-ignore-next-line
        foreach (App::getBean('AutoRunProcessManager')->getProcesses() as $k => $process)
        {
            if (\is_array($process))
            {
                ProcessManager::runWithManager($process['process'], $process['args'] ?? [], null, null, $k);
            }
            else
            {
                ProcessManager::runWithManager($process);
            }
        }
    }
}
