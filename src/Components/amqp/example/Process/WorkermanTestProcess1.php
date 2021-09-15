<?php

declare(strict_types=1);

namespace AMQPApp\Process;

use Imi\AMQP\Contract\IConsumer;
use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Workerman\Process\Annotation\Process;
use Imi\Workerman\Process\BaseProcess;
use Workerman\Worker;

/**
 * @Process(name="TestProcess1")
 */
class WorkermanTestProcess1 extends BaseProcess
{
    /**
     * @Inject("TestConsumer")
     *
     * @var \AMQPApp\AMQP\Test\TestConsumer
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
