<?php

declare(strict_types=1);

namespace KafkaApp\Process;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Kafka\Contract\IConsumer;
use Imi\Process\Annotation\Process;
use Imi\Swoole\Process\BaseProcess;

/**
 * @Process(name="TestProcess")
 */
class TestProcess extends BaseProcess
{
    /**
     * @Inject("TestConsumer")
     *
     * @var \KafkaApp\Kafka\Test\TestConsumer
     */
    protected $testConsumer;

    public function run(\Swoole\Process $process): void
    {
        $this->runConsumer($this->testConsumer);
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
