<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Server\ServerManager;
use Imi\Swoole\Process\ProcessManager;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Util\ImiPriority;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER", priority=ImiPriority::IMI_MIN)
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
