<?php

declare(strict_types=1);

namespace Imi\Swoole\Cron\Listener;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Server\Event\PipeMessageEvent;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;

#[Listener(eventName: 'imi.pipe_message.cronTask')]
class WorkerPartPipeMessage implements IEventListener
{
    #[Inject(name: 'CronWorker')]
    protected \Imi\Cron\CronWorker $cronWorker;

    /**
     * @param PipeMessageEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (ProcessType::WORKER !== App::get(ProcessAppContexts::PROCESS_TYPE))
        {
            return;
        }
        $data = $e->data['data'];
        $this->cronWorker->exec($data['id'], $data['data'], $data['task'], $data['type']);
    }
}
