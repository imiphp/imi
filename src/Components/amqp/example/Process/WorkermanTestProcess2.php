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
 * @Process(name="TestProcess2")
 */
class WorkermanTestProcess2 extends BaseProcess
{
    /**
     * @Inject("TestConsumer2")
     *
     * @var \AMQPApp\AMQP\Test2\TestConsumer2
     */
    protected $testConsumer2;

    public function run(Worker $process): void
    {
        $this->runConsumer($this->testConsumer2);
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
