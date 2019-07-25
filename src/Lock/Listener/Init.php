<?php
namespace Imi\Lock\Listener;

use Imi\Main\Helper;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Lock\Lock;

/**
 * @Listener(eventName="IMI.INITED",priority=19940200)
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
        foreach(Helper::getMains() as $main)
        {
            $config = $main->getConfig();
            foreach($config['lock']['list'] ?? [] as $id => $option)
            {
                Lock::add($id, $option);
            }
        }
    }
}
