<?php

declare(strict_types=1);

namespace Imi\Kafka\Base;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Kafka\Annotation\Consumer as ConsumerAnnotation;
use Imi\Kafka\Contract\IConsumer;
use Imi\Kafka\Pool\KafkaPool;
use longlang\phpkafka\Consumer\ConsumeMessage;
use longlang\phpkafka\Consumer\Consumer;
use function Yurun\Swoole\Coroutine\goWait;

/**
 * 消费者基类.
 */
abstract class BaseConsumer implements IConsumer
{
    /**
     * @var ConsumerAnnotation
     */
    protected $consumerAnnotation;

    /**
     * @var Consumer|null
     */
    protected $consumer = null;

    /**
     * @var bool
     */
    protected $running = false;

    public function __construct()
    {
        $this->initConfig();
    }

    /**
     * 初始化配置.
     *
     * @return void
     */
    protected function initConfig()
    {
        $class = BeanFactory::getObjectClass($this);
        $this->consumerAnnotation = AnnotationManager::getClassAnnotations($class, ConsumerAnnotation::class)[0] ?? null;
    }

    /**
     * 运行消费循环.
     *
     * @return void
     */
    public function run()
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
                goWait(function () use ($message, $consumer) {
                    $this->consume($message);
                    $consumer->ack($message);
                });
            }
        }
    }

    /**
     * 停止消费循环.
     *
     * @return void
     */
    public function stop()
    {
        $this->running = false;
    }

    /**
     * 关闭.
     *
     * @return void
     */
    public function close()
    {
        $this->consumer->close();
        $this->consumer = null;
    }

    /**
     * 消费任务
     *
     * @return mixed
     */
    abstract protected function consume(ConsumeMessage $message);
}
