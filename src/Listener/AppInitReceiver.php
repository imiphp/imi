<?php
namespace Imi\Listener;

use Imi\Worker;
use Imi\Event\Event;
use Swoole\Coroutine;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Param\PipeMessageEventParam;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Event\Listener\IPipeMessageEventListener;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.PIPE_MESSAGE")
 */
class AppInitReceiver implements IPipeMessageEventListener
{
    /**
     * 事件处理方法
     * @param PipeMessageEventParam $e
     * @return void
     */
    public function handle(PipeMessageEventParam $e)
    {
        if('app.inited' === $e->message && 0 === $e->workerID)
        {
            // worker 初始化
            Worker::inited();
            foreach($GLOBALS['WORKER_START_END_RESUME_COIDS'] as $id)
            {
                Coroutine::resume($id);
            }
            unset($GLOBALS['WORKER_START_END_RESUME_COIDS']);

            // 触发项目的workerstart事件
            Event::trigger('IMI.MAIN_SERVER.WORKER.START.APP', [
                'server'    =>    $e->server,
                'workerID'    =>    Worker::getWorkerID(),
            ], $e->getTarget(), WorkerStartEventParam::class);

            $e->stopPropagation();
        }
    }
}