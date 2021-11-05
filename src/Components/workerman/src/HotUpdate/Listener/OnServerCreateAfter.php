<?php

declare(strict_types=1);

namespace Imi\Workerman\HotUpdate\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Server\ServerManager;
use Imi\Workerman\Server\Contract\IWorkermanServer;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER")
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
        // 热更新
        if (Config::get('@app.beans.hotUpdate.status', true))
        {
            // @phpstan-ignore-next-line
            App::getBean('AutoRunProcessManager')->add('hotUpdate', 'hotUpdate');
        }
    }
}
