<?php
namespace Imi\Tool\Listener;

use Imi\Tool\Tool;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener(eventName="IMI.INITED", priority=19940310)
 */
class Init implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        Tool::initTool();
    }

}