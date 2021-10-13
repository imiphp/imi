<?php

declare(strict_types=1);

namespace Imi\Swoole\HotUpdate\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER")
 * @Listener(eventName="IMI.CO_SERVER.START",priority=Imi\Util\ImiPriority::IMI_MAX)
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
        // 热更新
        if (Config::get('@app.beans.hotUpdate.status', true))
        {
            App::getBean('AutoRunProcessManager')->add('hotUpdate', 'hotUpdate');
        }
    }
}
