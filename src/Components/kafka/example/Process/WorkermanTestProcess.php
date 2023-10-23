<?php

declare(strict_types=1);

namespace KafkaApp\Process;

use Imi\Aop\Annotation\Inject;
use Imi\Kafka\Contract\IConsumer;
use Imi\Log\Log;
use Imi\Workerman\Process\Annotation\Process;
use Imi\Workerman\Process\BaseProcess;
use Workerman\Worker;

/**
 * @Process(name="TestProcess")
 */
class WorkermanTestProcess extends BaseProcess
{
    /**
     * @Inject("TestConsumerWorkerman")
     *
     * @var \KafkaApp\Kafka\Test\TestConsumerWorkerman
     */
    protected $testConsumer;

    public function run(Worker $process): void
    {
        $this->runConsumer($this->testConsumer);
    }

    private function runConsumer(IConsumer $consumer): void
    {
        try
        {
            $consumer->run();
        }
        catch (\Throwable $th)
        {
            Log::error($th);
            sleep(3);
            $this->runConsumer($consumer);
        }
    }
}
