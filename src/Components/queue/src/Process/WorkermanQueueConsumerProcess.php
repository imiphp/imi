<?php

declare(strict_types=1);

namespace Imi\Queue\Process;

use Imi\Aop\Annotation\Inject;
use Imi\Log\Log;
use Imi\Queue\Service\QueueService;
use Imi\Workerman\Process\Annotation\Process;
use Imi\Workerman\Process\BaseProcess;
use Imi\Workerman\Process\ProcessManager;
use Imi\Workerman\Server\WorkermanServerWorker;
use Workerman\Worker;

if (\Imi\Util\Imi::checkAppType('workerman'))
{
    /**
     * Workerman 队列消费进程.
     *
     * @Process(name="QueueConsumer")
     */
    class WorkermanQueueConsumerProcess extends BaseProcess
    {
        /**
         * @Inject("imiQueue")
         */
        protected QueueService $imiQueue;

        public function run(Worker $worker): void
        {
            WorkermanServerWorker::clearAll();

            $imiQueue = $this->imiQueue;
            foreach ($imiQueue->getList() as $name => $arrayConfig)
            {
                $config = $imiQueue->getQueueConfig($name);
                if (!$config->getAutoConsumer())
                {
                    continue;
                }
                $process = $config->getProcess();
                for ($i = 0; $i < $process; ++$i)
                {
                    ProcessManager::newProcess('WorkermanQueueWorker', [
                        'config' => $config,
                    ], $name . '-' . $i);
                }
            }

            WorkermanServerWorker::runAll();
            if (!isset($name))
            {
                Log::warning('@app.beans.imiQueue.list is empty');
                while (true)
                {
                    sleep(86400);
                }
            }
        }
    }
}
