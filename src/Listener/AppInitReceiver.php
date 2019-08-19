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
            while(!Worker::isInited())
            {
                Coroutine::sleep(0.01);
            }

            while(isset($GLOBALS['WORKER_START_END_RESUME_COIDS']))
            {
                $coids = $GLOBALS['WORKER_START_END_RESUME_COIDS'];
                unset($GLOBALS['WORKER_START_END_RESUME_COIDS']);
                foreach($coids as $id)
                {
                    Coroutine::resume($id);
                }
            }

            $e->stopPropagation();
        }
    }
}