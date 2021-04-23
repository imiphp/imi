<?php

namespace Imi\Lock\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Lock\Lock;
use Imi\Main\Helper;

/**
 * @Listener(eventName="IMI.INITED",priority=19940200)
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
        foreach (Helper::getMains() as $main)
        {
            $list = $main->getConfig()['lock']['list'] ?? [];
            if ($list)
            {
                foreach ($list as $id => $option)
                {
                    Lock::add($id, $option);
                }
            }
        }
    }
}
