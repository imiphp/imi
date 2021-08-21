<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\ConnectionContext\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\RequestContext;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Event\Listener\IAppInitEventListener;
use Imi\Util\Imi;

/**
 * @Listener(eventName="IMI.APP.INIT")
 */
class AppInit implements IAppInitEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        foreach (ServerManager::getServers(ISwooleServer::class) as $server)
        {
            if ($server->isLongConnection())
            {
                RequestContext::set('server', $server);
                $server->getBean('ConnectionContextStore');
                if (Imi::getClassPropertyValue('ServerGroup', 'status'))
                {
                    /** @var \Imi\Server\Group\Handler\IGroupHandler $groupHandler */
                    $groupHandler = $server->getBean(Imi::getClassPropertyValue('ServerGroup', 'groupHandler'));
                    $groupHandler->startup();
                }
            }
        }
    }
}
