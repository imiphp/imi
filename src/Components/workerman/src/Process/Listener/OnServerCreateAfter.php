<?php

declare(strict_types=1);

namespace Imi\Workerman\Process\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Server\ServerManager;
use Imi\Workerman\Process\ProcessManager;
use Imi\Workerman\Server\Contract\IWorkermanServer;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER", priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class OnServerCreateAfter implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $servers = ServerManager::getServers();
        $server = reset($servers);
        if (!$server instanceof IWorkermanServer)
        {
            return;
        }
        // @phpstan-ignore-next-line
        foreach (App::getBean('AutoRunProcessManager')->getProcesses() as $k => $process)
        {
            if (\is_array($process))
            {
                ProcessManager::newProcess($process['process'], $process['args'] ?? [], $k);
            }
            else
            {
                ProcessManager::newProcess($process);
            }
        }
    }
}
