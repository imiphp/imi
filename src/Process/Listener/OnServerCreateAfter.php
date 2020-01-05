<?php
namespace Imi\Process\Listener;

use Imi\App;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Process\ProcessManager;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER",priority=Imi\Util\ImiPriority::IMI_MIN)
 * @Listener(eventName="IMI.CO_SERVER.START",priority=Imi\Util\ImiPriority::IMI_MIN)
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
        foreach(App::getBean('AutoRunProcessManager')->getProcesses() as $k => $process)
        {
            if(is_array($process))
            {
                ProcessManager::runWithManager($process['process'], $process['args'] ?? [], $k);
            }
            else
            {
                ProcessManager::runWithManager($process);
            }
        }
    }
}