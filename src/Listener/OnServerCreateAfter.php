<?php
namespace Imi\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Process\ProcessManager;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER")
 */
class OnServerCreateAfter implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        // 热更新
        if(Config::get('@app.beans.hotUpdate.status', true))
        {
            ProcessManager::runWithManager('hotUpdate');
        }
    }
}