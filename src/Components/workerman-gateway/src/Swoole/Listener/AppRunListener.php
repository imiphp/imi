<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Listener;

use GatewayWorker\Lib\Gateway;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Core\CoreEvents;
use Imi\Event\IEventListener;

if (\Imi\Util\Imi::checkAppType('swoole'))
{
    #[Listener(eventName: CoreEvents::APP_RUN, one: true)]
    class AppRunListener implements IEventListener
    {
        /**
         * 事件处理方法.
         */
        public function handle(\Imi\Event\Contract\IEvent $e): void
        {
            if ($registerAddress = Config::get('@app.mainServer.workermanGateway.registerAddress'))
            {
                Gateway::$registerAddress = $registerAddress;
                // @phpstan-ignore-next-line
                if (isset(Gateway::$persistentConnection))
                {
                    Gateway::$persistentConnection = false;
                }
            }
        }
    }
}
