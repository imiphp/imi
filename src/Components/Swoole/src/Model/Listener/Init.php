<?php

declare(strict_types=1);

namespace Imi\Swoole\Model\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Util\Imi;
use Imi\Util\MemoryTableManager;

/**
 * @Listener(eventName="IMI.SWOOLE.SERVER.BEFORE_START")
 */
class Init implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        MemoryTableManager::init();
    }
}
