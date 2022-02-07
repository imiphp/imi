<?php

declare(strict_types=1);

namespace Imi\AMQP\Base\Traits;

use Imi\AMQP\Annotation\Connection;
use Imi\AMQP\Annotation\Consumer;
use Imi\AMQP\Annotation\Exchange;
use Imi\AMQP\Annotation\Publisher;
use Imi\AMQP\Annotation\Queue;
use Imi\AMQP\Pool\AMQP;
use Imi\AMQP\Pool\AMQPPool;
use Imi\AMQP\Swoole\AMQPSwooleConnection;
use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Log\Log;
use Imi\Swoole\Util\Coroutine;
use Imi\Util\Imi;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

trait TAMQP
{
    /**
     * @Inject("AMQP")
     */
    protected AMQP $amqp;

    /**
     * 连接.
     */
    protected ?AbstractConnection $connection = null;

    /**
     * 频道.
     */
    protected ?AMQPChannel $channel = null;

    /**
     * 队列配置列表.
     *
     * @var \Imi\AMQP\Annotation\Queue[]
     */
    protected array $queues = [];

    /**
     * 交换机配置列表.
     *
     * @var \Imi\AMQP\Annotation\Exchange[]
     */
    protected array $exchanges = [];

    /**
     * 发布者列表.
     *
     * @var \Imi\AMQP\Annotation\Publisher[]
     */
    protected array $publishers = [];

    /**
     * 消费者列表.
     *
     * @var \Imi\AMQP\Annotation\Consumer[]
     */
    protected array $consumers = [];

    protected ?Connection $connectionAnnotation;

    /**
     * 连接池名称.
     */
    protected ?string $poolName = null;

    protected bool $isSwoole = false;

    /**
     * 初始化配置.
     */
    protected function initConfig(): void
    {
        $class = BeanFactory::getObjectClass($this);
        $annotations = AnnotationManager::getClassAnnotations($class, [
            Queue::class,
            Exchange::class,
            Publisher::class,
            Consumer::class,
            Connection::class,
        ]);
        $this->queues = $annotations[Queue::class];
        $this->exchanges = $annotations[Exchange::class];
        $this->publishers = $annotations[Publisher::class];
        $this->consumers = $annotations[Consumer::class];
        $this->connectionAnnotation = $annotations[Connection::class][0] ?? null;
        $this->isSwoole = Imi::checkAppType('swoole');
    }

    /**
     * 获取连接对象
     */
    protected function getConnection(): AbstractConnection
    {
        $poolName = null;
        if (null === $this->poolName)
        {
            $class = BeanFactory::getObjectClass($this);
            $connectionByPool = false;
            $connectionAnnotation = $this->connectionAnnotation;
            if ($connectionAnnotation)
            {
                if (null === $connectionAnnotation->poolName)
                {
                    if (!(null !== $connectionAnnotation->host && null !== $connectionAnnotation->port && null !== $connectionAnnotation->user && null !== $connectionAnnotation->password))
                    {
                        $connectionByPool = true;
                    }
                }
            }
            else
            {
                $connectionByPool = true;
            }
            if ($connectionByPool)
            {
                $poolName = $connectionAnnotation->poolName ?? $this->amqp->getDefaultPoolName();
            }
        }
        else
        {
            $connectionByPool = true;
            $poolName = $this->poolName;
        }
        if ($connectionByPool || $poolName)
        {
            return AMQPPool::getInstance($poolName);
        }
        elseif (isset($connectionAnnotation))
        {
            if ($this->isSwoole && Coroutine::isIn())
            {
                $className = AMQPSwooleConnection::class;
            }
            else
            {
                $className = AMQPStreamConnection::class;
            }

            return new $className(
                $connectionAnnotation->host,
                $connectionAnnotation->port,
                $connectionAnnotation->user,
                $connectionAnnotation->password,
                $connectionAnnotation->vhost,
                $connectionAnnotation->insist,
                $connectionAnnotation->loginMethod, $connectionAnnotation->loginResponse,
                $connectionAnnotation->locale, $connectionAnnotation->connectionTimeout,
                $connectionAnnotation->readWriteTimeout,
                $connectionAnnotation->context,
                $connectionAnnotation->keepalive,
                $connectionAnnotation->heartbeat,
                $connectionAnnotation->channelRpcTimeout
            );
        }
        else
        {
            throw new \RuntimeException('Annotation @Connection does not found');
        }
    }

    /**
     * 定义.
     */
    protected function declare(): void
    {
        foreach ($this->exchanges as $exchange)
        {
            Log::debug(sprintf('exchangeDeclare: %s, type: %s', $exchange->name, $exchange->type));
            // @phpstan-ignore-next-line
            $this->channel->exchange_declare($exchange->name, $exchange->type, $exchange->passive, $exchange->durable, $exchange->autoDelete, $exchange->internal, $exchange->nowait, new AMQPTable($exchange->arguments), $exchange->ticket);
        }
        foreach ($this->queues as $queue)
        {
            Log::debug(sprintf('queueDeclare: %s', $queue->name));
            $this->channel->queue_declare($queue->name, $queue->passive, $queue->durable, $queue->exclusive, $queue->autoDelete, $queue->nowait, new AMQPTable($queue->arguments), $queue->ticket);
        }
    }

    /**
     * 定义发布者.
     */
    protected function declarePublisher(): void
    {
        $this->declare();
        foreach ($this->publishers as $publisher)
        {
            foreach ((array) $publisher->queue as $queueName)
            {
                if ('' === $queueName)
                {
                    continue;
                }
                foreach ((array) $publisher->exchange as $exchangeName)
                {
                    Log::debug(sprintf('queueBind: %s, exchangeName: %s, routingKey: %s', $queueName, $exchangeName, $publisher->routingKey));
                    $this->channel->queue_bind($queueName, $exchangeName, $publisher->routingKey);
                }
            }
        }
    }

    /**
     * 定义消费者.
     */
    protected function declareConsumer(): void
    {
        $this->declare();
        foreach ($this->consumers as $consumer)
        {
            foreach ((array) $consumer->queue as $queueName)
            {
                foreach ((array) $consumer->exchange as $exchangeName)
                {
                    Log::debug(sprintf('queueBind: %s, exchangeName: %s, routingKey: %s', $queueName, $exchangeName, $consumer->routingKey));
                    $this->channel->queue_bind($queueName, $exchangeName, $consumer->routingKey);
                }
            }
        }
    }

    /**
     * Get 连接.
     */
    public function getAMQPConnection(): AbstractConnection
    {
        if (!$this->connection)
        {
            $this->connection = $this->getConnection();
        }

        return $this->connection;
    }

    /**
     * Get 频道.
     */
    public function getAMQPChannel(): AMQPChannel
    {
        if (!$this->channel || !$this->channel->is_open())
        {
            $this->channel = $this->getAMQPConnection()->channel();
        }

        return $this->channel;
    }
}
