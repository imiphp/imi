<?php

declare(strict_types=1);

namespace Imi\Kafka\Base;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Kafka\Annotation\Consumer as ConsumerAnnotation;
use Imi\Kafka\Contract\IConsumer;
use Imi\Kafka\Pool\KafkaPool;
use Imi\Util\Imi;
use longlang\phpkafka\Consumer\ConsumeMessage;
use longlang\phpkafka\Consumer\Consumer;
use function Yurun\Swoole\Coroutine\goWait;

/**
 * 消费者基类.
 */
abstract class BaseConsumer implements IConsumer
{
    protected ConsumerAnnotation $consumerAnnotation;

    protected ?Consumer $consumer = null;

    protected bool $running = false;

    public function __construct()
    {
        $this->initConfig();
    }

    /**
     * 初始化配置.
     */
    protected function initConfig(): void
    {
        $class = BeanFactory::getObjectClass($this);
        $this->consumerAnnotation = AnnotationManager::getClassAnnotations($class, ConsumerAnnotation::class)[0] ?? null;
    }

    /**
     * 运行消费循环.
     */
    public function run(): void
    {
        $consumerAnnotation = $this->consumerAnnotation;
        $config = [];
        if (null !== $consumerAnnotation->groupId)
        {
            $config['groupId'] = $consumerAnnotation->groupId;
        }
        $consumer = $this->consumer = KafkaPool::createConsumer($consumerAnnotation->poolName, $consumerAnnotation->topic, $config);
        $this->running = true;
        while ($this->running)
        {
            $message = $consumer->consume();
            if ($message)
            {
                if (Imi::checkAppType('swoole'))
                {
                    goWait(function () use ($message, $consumer) {
                        $this->consume($message);
                        $consumer->ack($message);
                    });
                }
                else
                {
                    $this->consume($message);
                    $consumer->ack($message);
                }
            }
        }
    }

    /**
     * 停止消费循环.
     */
    public function stop(): void
    {
        $this->running = false;
    }

    /**
     * 关闭.
     */
    public function close(): void
    {
        $this->consumer->close();
        $this->consumer = null;
    }

    /**
     * 消费任务
     */
    abstract protected function consume(ConsumeMessage $message): void;
}
