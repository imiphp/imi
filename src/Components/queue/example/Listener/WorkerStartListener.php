<?php

declare(strict_types=1);

namespace QueueApp\Listener;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Queue\Model\Message;
use Swoole\Coroutine;

/**
 * @Listener("IMI.MAIN_SERVER.WORKER.START.APP")
 */
class WorkerStartListener implements IEventListener
{
    /**
     * @Inject("imiQueue")
     *
     * @var \Imi\Queue\Service\QueueService
     */
    protected $imiQueue;

    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        // 每 1 秒投递进 test1 队列
        Coroutine::create(function () {
            while (true)
            {
                $message = new Message();
                $message->setMessage((string) time());
                $this->imiQueue->getQueue('test1')->push($message);
                sleep(1);
            }
        });
        // 每 3 秒投递进 test2 队列
        Coroutine::create(function () {
            while (true)
            {
                $message = new Message();
                $message->setMessage((string) time());
                $this->imiQueue->getQueue('test2')->push($message);
                sleep(3);
            }
        });
    }
}
