<?php

declare(strict_types=1);

namespace KafkaApp\Process;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Kafka\Contract\IConsumer;
use Imi\Workerman\Process\Annotation\Process;
use Imi\Workerman\Process\BaseProcess;
use Workerman\Worker;

/**
 * @Process(name="TestProcess")
 */
class WorkermanTestProcess extends BaseProcess
{
    /**
     * @Inject("TestConsumer")
     *
     * @var \KafkaApp\Kafka\Test\TestConsumer
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
            /** @var \Imi\Log\ErrorLog $errorLog */
            $errorLog = App::getBean('ErrorLog');
            $errorLog->onException($th);
            sleep(3);
            $this->runConsumer($consumer);
        }
    }
}
