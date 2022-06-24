<?php

declare(strict_types=1);

namespace Imi\Queue\Process;

use Imi\App;
use Imi\Workerman\Process\Annotation\Process;
use Imi\Workerman\Process\BaseProcess;
use Workerman\Worker;

if (\Imi\Util\Imi::checkAppType('workerman'))
{
    /**
     * Workerman 队列消费进程.
     *
     * @Process(name="WorkermanQueueWorker")
     */
    class WorkermanQueueWorkerProcess extends BaseProcess
    {
        public function run(Worker $worker): void
        {
            /** @var \Imi\Queue\Model\QueueConfig $config */
            $config = $this->data['config'];
            /** @var \Imi\Queue\Service\BaseQueueConsumer $queueConsumer */
            $queueConsumer = App::getBean($config->getConsumer(), $config->getName());
            $queueConsumer->start();
        }
    }
}
