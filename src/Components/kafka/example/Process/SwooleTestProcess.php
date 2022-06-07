<?php

declare(strict_types=1);

namespace KafkaApp\Process;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Event\Event;
use Imi\Kafka\Contract\IConsumer;
use Imi\Swoole\Process\Annotation\Process;
use Imi\Swoole\Process\BaseProcess;
use Imi\Swoole\Util\Coroutine;
use Imi\Util\ImiPriority;

/**
 * @Process(name="TestProcess")
 */
class SwooleTestProcess extends BaseProcess
{
    /**
     * @Inject("TestConsumer")
     *
     * @var \KafkaApp\Kafka\Test\TestConsumer
     */
    protected $testConsumer;

    private bool $running = false;

    public function run(\Swoole\Process $process): void
    {
        $this->running = true;
        $this->runConsumer($this->testConsumer);
        $cid = Coroutine::getCid();
        Event::on('IMI.PROCESS.END', function () use ($cid) {
            $this->running = false;
            $this->testConsumer->close();
            Coroutine::resume($cid);
        }, ImiPriority::IMI_MAX);
        Coroutine::yield();
    }

    private function runConsumer(IConsumer $consumer): void
    {
        Coroutine::create(function () use ($consumer) {
            try
            {
                $consumer->run();
            }
            catch (\Throwable $th)
            {
                /** @var \Imi\Log\ErrorLog $errorLog */
                $errorLog = App::getBean('ErrorLog');
                $errorLog->onException($th);
                if ($this->running)
                {
                    sleep(3);
                    $this->runConsumer($consumer);
                }
            }
        });
    }
}
