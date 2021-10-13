<?php

declare(strict_types=1);

namespace QueueApp\Listener;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Queue\Model\Message;
use Imi\Timer\Timer;

/**
 * @Listener("IMI.MAIN_SERVER.WORKER.START.APP")
 * @Listener("IMI.WORKERMAN.SERVER.WORKER_START")
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
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        // 每 1 秒投递进 test1 队列
        Timer::tick(1000, function () {
            $message = new Message();
            $message->setMessage((string) time());
            $this->imiQueue->getQueue('test1')->push($message);
        });
        // 每 3 秒投递进 test2 队列
        Timer::tick(3000, function () {
            $message = new Message();
            $message->setMessage((string) time());
            $this->imiQueue->getQueue('test2')->push($message);
        });
    }
}
