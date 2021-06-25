<?php

namespace Imi\Queue\Process;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Process\Annotation\Process;
use Imi\Process\BaseProcess;
use Imi\Util\Imi;
use Swoole\Coroutine;
use Swoole\Event;

/**
 * 队列消费进程.
 *
 * @Process(name="QueueConsumer", unique=true, co=false)
 */
class QueueConsumerProcess extends BaseProcess
{
    /**
     * @Inject("imiQueue")
     *
     * @var \Imi\Queue\Service\QueueService
     */
    protected $imiQueue;

    /**
     * 消费者列表.
     *
     * @var \Imi\Queue\Service\BaseQueueConsumer[]
     */
    private $consumers = [];

    public function run(\Swoole\Process $process)
    {
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
        foreach ($processGroups as $group => $options)
        {
            $processPool = new \Imi\Process\Pool($options['process']);
            $configs = $options['configs'];
            $processPool->on('WorkerStart', function (\Imi\Process\Pool\WorkerEventParam $e) use ($group, $configs) {
                go(function () use ($group, $configs) {
                    \Swoole\Runtime::enableCoroutine(true);
                    App::initWorker();
                    Imi::setProcessName('process', [
                        'processName'   => 'QueueConsumer-' . $group,
                    ]);
                    /** @var \Imi\Queue\Model\QueueConfig[] $configs */
                    foreach ($configs as $config)
                    {
                        Coroutine::create(function () use ($config) {
                            /* @var \Imi\Queue\Service\BaseQueueConsumer $queueConsumer */
                            $this->consumers[] = $queueConsumer = App::getBean($config->getConsumer(), $config->getName());
                            $queueConsumer->start();
                        });
                    }
                });
            });
            // 工作进程退出事件-可选
            $processPool->on('WorkerExit', function (\Imi\Process\Pool\WorkerEventParam $e) {
                // 做一些释放操作
                foreach ($this->consumers as $consumer)
                {
                    $consumer->stop();
                }
            });
            $processPool->start();
        }
        Event::wait();
    }
}
