<?php

declare(strict_types=1);

namespace AMQPApp\Process;

use Imi\AMQP\Contract\IConsumer;
use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Event\Event;
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
     * @var \AMQPApp\AMQP\Test\TestConsumer
     */
    protected $testConsumer;

    /**
     * @Inject("TestConsumer2")
     *
     * @var \AMQPApp\AMQP\Test2\TestConsumer2
     */
    protected $testConsumer2;

    private bool $running = false;

    public function run(\Swoole\Process $process): void
    {
        $this->running = true;
        $this->runConsumer($this->testConsumer);
        $this->runConsumer($this->testConsumer2);
        $channel = new \Swoole\Coroutine\Channel();
        Event::on('IMI.PROCESS.END', function () use ($channel) {
            $this->running = false;
            $this->testConsumer->close();
            $this->testConsumer2->close();
            $channel->push(1);
        }, ImiPriority::IMI_MAX);
        $channel->pop();
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
