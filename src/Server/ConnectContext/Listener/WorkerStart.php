<?php
namespace Imi\Server\ConnectContext\Listener;

use Imi\ServerManage;
use Imi\RequestContext;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Bean\BeanProxy;
use Imi\Util\Imi;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START")
 */
class WorkerStart implements IWorkerStartEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(WorkerStartEventParam $e)
    {
        if(!$e->server->getSwooleServer()->taskworker)
        {
            RequestContext::create();
            foreach(ServerManage::getServers() as $server)
            {
                RequestContext::set('server', $server);
                $server->getBean('ConnectContextStore')->getHandler();
                if(Imi::getClassPropertyValue('ServerGroup', 'status'))
                {
                    $server->getBean(Imi::getClassPropertyValue('ServerGroup', 'groupHandler'));
                }
            }
            RequestContext::destroy();
        }
    }
}