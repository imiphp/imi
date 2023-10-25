<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Listener;

use GatewayWorker\Lib\Gateway;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

if (\Imi\Util\Imi::checkAppType('workerman'))
{
    #[Listener(eventName: 'IMI.APP_RUN', one: true)]
    class AppRunListener implements IEventListener
    {
        /**
         * 事件处理方法.
         */
        public function handle(EventParam $e): void
        {
            foreach (Config::get('@app.workermanServer', []) as $item)
            {
                if (isset($item['configs']['registerAddress']))
                {
                    Gateway::$registerAddress = $item['configs']['registerAddress'];
                    // @phpstan-ignore-next-line
                    if (isset(Gateway::$persistentConnection))
                    {
                        Gateway::$persistentConnection = false;
                    }
                    break;
                }
            }
        }
    }
}
