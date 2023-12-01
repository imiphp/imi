<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Db\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Db\Event\DbEvents;
use Imi\Db\Event\Param\DbPrepareEventParam;
use Imi\Event\IEventListener;
use Imi\Log\Log;

#[Listener(eventName: DbEvents::PREPARE)]
class DbPrepareListener implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param DbPrepareEventParam $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (false !== App::get('DB_LOG'))
        {
            if ($e->throwable)
            {
                Log::error(sprintf('[%s] %s', $e->throwable->getMessage(), $e->sql));
            }
            else
            {
                Log::info(sprintf('[prepare] %s', $e->sql));
            }
        }
    }
}
