<?php

declare(strict_types=1);

namespace QueueApp\Listener;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Queue\Model\Message;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Timer\Timer;

#[Listener(eventName: SwooleEvents::WORKER_APP_START, one: true)]
class WorkerStartListener implements IEventListener
{
    #[Inject(name: 'imiQueue')]
    protected \Imi\Queue\Service\QueueService $imiQueue;

    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        // 每 1 秒投递进 test1 队列
        Timer::tick(1000, function (): void {
            $message = new Message();
            $message->setMessage((string) time());
            $this->imiQueue->getQueue('test1')->push($message);
        });
        // 每 3 秒投递进 test2 队列
        Timer::tick(3000, function (): void {
            $message = new Message();
            $message->setMessage((string) time());
            $this->imiQueue->getQueue('test2')->push($message);
        });
    }
}
