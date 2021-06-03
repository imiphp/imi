<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Cron\Annotation\Cron;
use Imi\Cron\Util\CronUtil;
use Imi\Swoole\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;
use Imi\Swoole\Test\HttpServer\Cron\CronDWorker;
use Imi\Worker;
use function Yurun\Swoole\Coroutine\goWait;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START")
 */
class WorkerStart implements IWorkerStartEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(WorkerStartEventParam $e): void
    {
        if (0 === Worker::getWorkerId())
        {
            goWait(function () {
                sleep(1);
                $cron = new Cron();
                $cron->id = 'CronRandomWorkerTest';
                $cron->second = '3n';
                $cron->type = 'random_worker';
                CronUtil::addCron($cron, CronDWorker::class);
                var_dump('add');
            });
        }
    }
}
