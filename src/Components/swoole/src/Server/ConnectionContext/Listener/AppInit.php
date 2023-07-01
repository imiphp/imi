<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\ConnectionContext\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Event\Listener\IAppInitEventListener;
use Imi\Swoole\Server\Traits\TServerPortInfo;
use Imi\Util\Imi;

/**
 * @Listener(eventName="IMI.APP.INIT")
 */
class AppInit implements IAppInitEventListener
{
    use TServerPortInfo;

    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        /** @var ISwooleServer|null $server */
        $server = ServerManager::getServer('main', ISwooleServer::class);
        if ($server)
        {
            $mainSwooleServer = $server->getSwooleServer();
            if (($serverStart = !$mainSwooleServer->manager_pid))
            {
                $this->outputServerInfo();
            }
        }
        foreach (ServerManager::getServers(ISwooleServer::class) as $server)
        {
            if ($server->isLongConnection())
            {
                RequestContext::set('server', $server);
                // @phpstan-ignore-next-line
                $server->getBean('ConnectionContextStore')->init();
                if (Imi::getClassPropertyValue('ServerGroup', 'status'))
                {
                    /** @var \Imi\Server\Group\Handler\IGroupHandler $groupHandler */
                    $groupHandler = $server->getBean(Imi::getClassPropertyValue('ServerGroup', 'groupHandler'));
                    $groupHandler->startup();
                }
            }
        }
        if ($serverStart ?? false)
        {
            Log::info('Server start');
        }
    }
}
