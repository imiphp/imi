<?php

namespace AMQPApp\Process;

use Imi\AMQP\Contract\IConsumer;
use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Process\Annotation\Process;
use Imi\Process\BaseProcess;

/**
 * @Process(name="TestProcess")
 */
class TestProcess extends BaseProcess
{
    /**
     * @Inject("TestConsumer")
     *
     * @var \AMQPApp\AMQP\Test\TestConsumer
     */
    protected $testConsumer;

    /**
     * @Inject("TestConsumer2")
     *
     * @var \AMQPApp\AMQP\Test2\TestConsumer2
     */
    protected $testConsumer2;

    public function run(\Swoole\Process $process)
    {
        $this->runConsumer($this->testConsumer);
        $this->runConsumer($this->testConsumer2);
        \Swoole\Coroutine::yield();
    }

    private function runConsumer(IConsumer $consumer): void
    {
        go(function () use ($consumer) {
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
        });
    }
}
