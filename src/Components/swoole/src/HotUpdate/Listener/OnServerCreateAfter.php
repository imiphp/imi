<?php

declare(strict_types=1);

namespace Imi\Swoole\HotUpdate\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Event\IEventListener;
use Imi\Server\Event\ServerEvents;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;

#[Listener(eventName: ServerEvents::AFTER_CREATE_SERVERS, one: true)]
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
        // 热更新
        // @phpstan-ignore-next-line
        if (Config::get('@app.beans.hotUpdate.status', true) && !IMI_IN_PHAR)
        {
            // @phpstan-ignore-next-line
            App::getBean('AutoRunProcessManager')->add('hotUpdate', 'hotUpdate');
        }
    }
}
