<?php

declare(strict_types=1);

namespace Imi\Swoole\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Log\Log;
use Imi\Swoole\Server\Event\Listener\IManagerStartEventListener;
use Imi\Swoole\Server\Event\Param\ManagerStartEventParam;
use Imi\Swoole\Server\Traits\TServerPortInfo;
use Imi\Swoole\Util\Imi;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.MANAGER.START")
 */
class ServerStart implements IManagerStartEventListener
{
    use TServerPortInfo;

    /**
     * {@inheritDoc}
     */
    public function handle(ManagerStartEventParam $e): void
    {
        $this->outputServerInfo();
        Log::info('Server start');
    }
}
