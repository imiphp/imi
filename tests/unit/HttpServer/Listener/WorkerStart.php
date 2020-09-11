<?php

namespace Imi\Test\HttpServer\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Cron\Annotation\Cron;
use Imi\Cron\Util\CronUtil;
use Imi\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Test\HttpServer\Cron\CronDWorker;
use Imi\Worker;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START")
 */
class WorkerStart implements IWorkerStartEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(WorkerStartEventParam $e)
    {
        if (0 === Worker::getWorkerID())
        {
            // go(function(){
            //     sleep(1);
            //     $cron = new Cron;
            //     $cron->id = 'CronRandomWorkerTest';
            //     $cron->second = '3n';
            //     $cron->type = 'random_worker';
            //     CronUtil::addCron($cron, CronDWorker::class);
            //     var_dump('add');
            // });
        }
    }
}
