<?php

declare(strict_types=1);

namespace Imi\Swoole\Model\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Util\MemoryTableManager;
use Imi\Util\Imi;

/**
 * @Listener(eventName="IMI.SWOOLE.SERVER.BEFORE_START")
 */
class Init implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        MemoryTableManager::init();
    }
}
