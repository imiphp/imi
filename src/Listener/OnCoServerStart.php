<?php
namespace Imi\Listener;

use Imi\App;
use Imi\Config;
use Imi\Util\Imi;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Process\ProcessManager;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener(eventName="IMI.CO_SERVER.START",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class OnCoServerStart implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        $object = $e->getTarget();
        // 进程PID记录
        $fileName = Imi::getRuntimePath(str_replace('\\', '-', App::getNamespace()) . '.pid');
        file_put_contents($fileName, json_encode([
            'masterPID'     => $object->getPID(),
            'managerPID'    => null,
        ]));
        // 热更新
        if(Config::get('@app.beans.hotUpdate.status', true))
        {
            ProcessManager::runWithManager('hotUpdate');
        }
    }
}
