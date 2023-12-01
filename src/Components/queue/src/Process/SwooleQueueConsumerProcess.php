<?php

declare(strict_types=1);

namespace Imi\Queue\Process;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Event\Event as ImiEvent;
use Imi\Log\Log;
use Imi\Process\Event\ProcessBeginEvent;
use Imi\Process\Event\ProcessEndEvent;
use Imi\Process\Event\ProcessEvents;
use Imi\Queue\Service\QueueService;
use Imi\Swoole\Process\Annotation\Process;
use Imi\Swoole\Process\BaseProcess;
use Imi\Swoole\Util\Coroutine;
use Imi\Swoole\Util\Imi;
use Imi\Util\ImiPriority;
use Swoole\Event;

if (\Imi\Util\Imi::checkAppType('swoole'))
{
    /**
     * Swoole 队列消费进程.
     */
    #[Process(name: 'QueueConsumer', unique: true, co: false)]
    class SwooleQueueConsumerProcess extends BaseProcess
    {
        #[Inject(name: 'imiQueue')]
        protected QueueService $imiQueue;

        /**
         * 消费者列表.
         *
         * @var \Imi\Queue\Service\BaseQueueConsumer[]
         */
        private array $consumers = [];

        public function run(\Swoole\Process $process): void
        {
            $running = true;
            \Imi\Event\Event::on(ProcessEvents::PROCESS_END, static function () use (&$running): void {
                $running = false;
            }, ImiPriority::IMI_MAX);
            $imiQueue = $this->imiQueue;
            $processGroups = [];
            foreach ($imiQueue->getList() as $name => $arrayConfig)
            {
                $config = $imiQueue->getQueueConfig($name);
                if (!$config->getAutoConsumer())
                {
                    continue;
                }
                $group = $config->getProcessGroup();
                $process = $config->getProcess();
                if (!isset($processGroups[$group]) || $process > $processGroups[$group]['process'])
                {
                    $processGroups[$group]['process'] = $process;
                }
                $processGroups[$group]['configs'][] = $config;
            }
            $processPools = [];
            foreach ($processGroups as $group => $options)
            {
                $processPools[] = $processPool = new \Imi\Swoole\Process\Pool($options['process']);
                $configs = $options['configs'];
                $processPool->on('WorkerStart', function (\Imi\Swoole\Process\Pool\WorkerEventParam $e) use ($group, $configs): void {
                    $processName = 'QueueConsumer-' . $group;
                    // 进程开始事件
                    ImiEvent::dispatch(new ProcessBeginEvent($processName, $e->getWorker()));
                    Imi::setProcessName('process', [
                        'processName'   => $processName,
                    ]);
                    /** @var \Imi\Queue\Model\QueueConfig[] $configs */
                    foreach ($configs as $config)
                    {
                        Coroutine::create(function () use ($config): void {
                            /** @var \Imi\Queue\Service\BaseQueueConsumer $queueConsumer */
                            $queueConsumer = $this->consumers[] = App::newInstance($config->getConsumer(), $config->getName());
                            $queueConsumer->start();
                        });
                    }
                });
                // 工作进程退出事件-可选
                $processPool->on('WorkerExit', function (\Imi\Swoole\Process\Pool\WorkerEventParam $e) use ($group): void {
                    // 做一些释放操作
                    foreach ($this->consumers as $consumer)
                    {
                        $consumer->stop();
                    }
                    // 进程结束事件
                    ImiEvent::dispatch(new ProcessEndEvent('QueueConsumer-' . $group, $e->getWorker()));
                });
                $processPool->start();
            }
            if ($processPools)
            {
                // @phpstan-ignore-next-line
                while ($running)
                {
                    foreach ($processPools as $processPool)
                    {
                        $processPool->wait(false);
                    }
                    Event::dispatch();
                    usleep(10000);
                }
            }
            else
            {
                Log::warning('@app.beans.imiQueue.list is empty');
                Coroutine::create(static function () use (&$running): void {
                    // @phpstan-ignore-next-line
                    while ($running)
                    {
                        sleep(1);
                    }
                });
                Event::wait();
            }
        }
    }
}
