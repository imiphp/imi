<?php

declare(strict_types=1);

namespace KafkaApp\Process;

use Imi\Aop\Annotation\Inject;
use Imi\Event\Event;
use Imi\Kafka\Contract\IConsumer;
use Imi\Log\Log;
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
     * @Inject("TestConsumerSwoole")
     *
     * @var \KafkaApp\Kafka\Test\TestConsumerSwoole
     */
    protected $testConsumer;

    private bool $running = false;

    public function run(\Swoole\Process $process): void
    {
        $this->running = true;
        $this->runConsumer($this->testConsumer);
        $channel = new \Swoole\Coroutine\Channel();
        Event::on('IMI.PROCESS.END', function () use ($channel): void {
            $this->running = false;
            $this->testConsumer->close();
            $channel->push(1);
        }, ImiPriority::IMI_MAX);
        $channel->pop();
    }

    private function runConsumer(IConsumer $consumer): void
    {
        Coroutine::create(function () use ($consumer): void {
            try
            {
                $consumer->run();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
                if ($this->running)
                {
                    sleep(3);
                    $this->runConsumer($consumer);
                }
            }
        });
    }
}
