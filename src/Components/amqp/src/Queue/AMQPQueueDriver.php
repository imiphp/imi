<?php

declare(strict_types=1);

namespace Imi\AMQP\Queue;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Queue\Contract\IMessage;
use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Enum\QueueType;
use Imi\Queue\Exception\QueueException;
use Imi\Queue\Model\QueueStatus;
use Imi\Redis\RedisManager;
use Imi\Util\Traits\TDataToProperty;

/**
 * AMQP 队列驱动.
 *
 * @Bean("AMQPQueueDriver")
 */
class AMQPQueueDriver implements IQueueDriver
{
    use TDataToProperty{
        __construct as private traitConstruct;
    }

    public const ROUTING_NORMAL = 'normal';

    public const ROUTING_DELAY = 'delay';

    public const ROUTING_TIMEOUT = 'timeout';

    public const ROUTING_FAIL = 'fail';

    /**
     * AMQP 连接池名称.
     *
     * @var string
     */
    protected $poolName;

    /**
     * 队列名称.
     *
     * @var string
     */
    protected $name;

    /**
     * 支持消息删除功能.
     *
     * 依赖 Redis
     *
     * @var bool
     */
    protected $supportDelete = true;

    /**
     * 支持消费超时队列功能.
     *
     * 依赖 Redis，并且自动增加一个队列
     *
     * @var bool
     */
    protected $supportTimeout = true;

    /**
     * 支持消费失败队列功能.
     *
     * 自动增加一个队列
     *
     * @var bool
     */
    protected $supportFail = true;

    /**
     * Redis 连接池名称.
     *
     * @var string
     */
    protected $redisPoolName;

    /**
     * Redis 键名前缀
     *
     * @var string
     */
    protected $redisPrefix = '';

    /**
     * 循环尝试 pop 的时间间隔，单位：秒.
     *
     * @var float
     */
    protected $timespan = 0.03;

    /**
     * 本地缓存的队列长度.
     *
     * @var int
     */
    protected $queueLength = 16;

    /**
     * 消息类名.
     *
     * @var string
     */
    protected $message = JsonAMQPMessage::class;

    /**
     * 发布者.
     *
     * @var \Imi\AMQP\Queue\QueuePublisher|null
     */
    private $publisher;

    /**
     * 延迟发布者.
     *
     * @var \Imi\AMQP\Queue\QueuePublisher|null
     */
    private $delayPublisher;

    /**
     * 消费者.
     *
     * @var \Imi\AMQP\Queue\QueueConsumer|null
     */
    private $consumer;

    /**
     * 超时队列发布者.
     *
     * @var \Imi\AMQP\Queue\QueuePublisher|null
     */
    private $timeoutPublisher;

    /**
     * 超时队列消费者.
     *
     * @var \Imi\AMQP\Queue\QueueConsumer|null
     */
    private $timeoutConsumer;

    /**
     * 失败队列发布者.
     *
     * @var \Imi\AMQP\Queue\QueuePublisher|null
     */
    private $failPublisher;

    /**
     * 失败队列消费者.
     *
     * @var \Imi\AMQP\Queue\QueueConsumer|null
     */
    private $failConsumer;

    /**
     * AMQP 的队列名称.
     *
     * @var string
     */
    private $queueName;

    /**
     * AMQP 的延迟队列名称.
     *
     * @var string
     */
    private $delayQueueName;

    /**
     * AMQP 的失败队列名称.
     *
     * @var string
     */
    private $failQueueName;

    /**
     * AMQP 的超时队列名称.
     *
     * @var string
     */
    private $timeoutQueueName;

    public function __construct(string $name, array $config = [])
    {
        $this->name = $name;
        $this->traitConstruct($config);

        $exchangeName = 'imi-' . $name;
        $this->queueName = $queueName = 'imi-queue-' . $name;
        $this->delayQueueName = $queueDelayName = 'imi-' . $name . '-delay';
        $exchanges = [
            [
                'name'  => $exchangeName,
            ],
        ];
        $queues = [
            [
                'name'          => $queueName,
                'routingKey'    => self::ROUTING_NORMAL,
            ],
        ];
        $publishers = [
            [
                'exchange'      => $exchangeName,
                'routingKey'    => self::ROUTING_NORMAL,
                'queue'         => $queueName,
            ],
        ];
        $consumers = [
            [
                'exchange'      => $exchangeName,
                'queue'         => $queueName,
                'routingKey'    => self::ROUTING_NORMAL,
                'message'       => $this->message,
            ],
        ];
        $delayQueues = [
            [
                'name'          => $queueDelayName,
                'arguments'     => [
                    'x-dead-letter-exchange'    => $exchangeName,
                    'x-dead-letter-routing-key' => self::ROUTING_NORMAL,
                ],
                'routingKey'    => self::ROUTING_DELAY,
            ],
        ];
        $delayPublishers = [
            [
                'exchange'   => $exchangeName,
                'routingKey' => self::ROUTING_DELAY,
                'queue'      => $queueDelayName,
            ],
        ];
        $this->publisher = BeanFactory::newInstance(QueuePublisher::class, $exchanges, $queues, $publishers, $this->poolName);
        $this->delayPublisher = BeanFactory::newInstance(QueuePublisher::class, $exchanges, $delayQueues, $delayPublishers, $this->poolName);
        $this->consumer = BeanFactory::newInstance(QueueConsumer::class, $this->queueLength, $exchanges, $queues, $consumers, $this->poolName);
        if ($this->supportTimeout)
        {
            $this->timeoutQueueName = $timeoutQueueName = ('imi-' . $name . '-timeout');
            $this->timeoutPublisher = BeanFactory::newInstance(QueuePublisher::class, $exchanges, [
                [
                    'name'          => $timeoutQueueName,
                    'routingKey'    => self::ROUTING_TIMEOUT,
                ],
            ], [
                [
                    'exchange'      => $exchangeName,
                    'routingKey'    => self::ROUTING_TIMEOUT,
                    'queue'         => $timeoutQueueName,
                ],
            ], $this->poolName);
            $this->timeoutConsumer = BeanFactory::newInstance(QueueConsumer::class, 1, $exchanges, [
                [
                    'name'          => $timeoutQueueName,
                    'routingKey'    => self::ROUTING_TIMEOUT,
                ],
            ], [
                [
                    'exchange'      => $exchangeName,
                    'routingKey'    => self::ROUTING_TIMEOUT,
                    'queue'         => $timeoutQueueName,
                    'message'       => $this->message,
                ],
            ], $this->poolName);
        }
        if ($this->supportFail)
        {
            $this->failQueueName = $failQueueName = ('imi-' . $name . '-fail');
            $this->failPublisher = BeanFactory::newInstance(QueuePublisher::class, $exchanges, [
                [
                    'name'          => $failQueueName,
                    'routingKey'    => self::ROUTING_FAIL,
                ],
            ], [
                [
                    'exchange'      => $exchangeName,
                    'routingKey'    => self::ROUTING_FAIL,
                    'queue'         => $failQueueName,
                ],
            ], $this->poolName);
            $this->failConsumer = BeanFactory::newInstance(QueueConsumer::class, 1, $exchanges, [
                [
                    'name'          => $failQueueName,
                    'routingKey'    => self::ROUTING_FAIL,
                ],
            ], [
                [
                    'exchange'      => $exchangeName,
                    'routingKey'    => self::ROUTING_FAIL,
                    'queue'         => $failQueueName,
                    'message'       => $this->message,
                ],
            ], $this->poolName);
        }
    }

    public function __destruct()
    {
        $this->publisher->close();
        $this->consumer->close();
        if ($this->failPublisher)
        {
            $this->failPublisher->close();
        }
        if ($this->failConsumer)
        {
            $this->failConsumer->close();
        }
        if ($this->timeoutPublisher)
        {
            $this->timeoutPublisher->close();
        }
        if ($this->timeoutConsumer)
        {
            $this->timeoutConsumer->close();
        }
        $this->delayPublisher->close();
    }

    /**
     * 获取队列名称.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 推送消息到队列，返回消息ID.
     */
    public function push(IMessage $message, float $delay = 0, array $options = []): string
    {
        $redis = RedisManager::getInstance($this->redisPoolName);
        $message->setMessageId($messageId = (string) $redis->incr($this->getRedisMessageIdKey()));
        $amqpMessage = new \Imi\AMQP\Message();
        $amqpMessage->setBody(json_encode($message->toArray()));
        if ($delay > 0)
        {
            $amqpMessage->setRoutingKey(self::ROUTING_DELAY);
            $amqpMessage->setProperties([
                'expiration'    => $delay * 1000,
            ]);
            $this->delayPublisher->publish($amqpMessage);
        }
        else
        {
            $amqpMessage->setRoutingKey(self::ROUTING_NORMAL);
            $this->publisher->publish($amqpMessage);
        }

        return $messageId;
    }

    /**
     * 从队列弹出一个消息.
     *
     * @param float $timeout 超时时间，单位：秒。值是-1时立即返回结果
     */
    public function pop(float $timeout = -1): ?IMessage
    {
        $time = $useTime = 0;
        do
        {
            if ($timeout > 0)
            {
                if ($time)
                {
                    $leftTime = $timeout - $useTime;
                    if ($leftTime > $this->timespan)
                    {
                        usleep((int) ($this->timespan * 1000000));
                    }
                }
                else
                {
                    $time = microtime(true);
                }
            }
            if ($this->supportTimeout)
            {
                $this->parseTimeoutMessages();
            }
            $result = $this->consumer->pop($this->timespan);
            if ($result)
            {
                $message = new QueueAMQPMessage();
                $message->setAmqpMessage($result);
                // 检查是否被删除
                if ($this->messageIsDeleted($message->getMessageId()))
                {
                    continue;
                }
                // 加入工作队列
                $redis = RedisManager::getInstance($this->redisPoolName);
                $workingTimeout = $message->getWorkingTimeout();
                if ($workingTimeout > 0)
                {
                    $score = microtime(true) + $workingTimeout;
                }
                else
                {
                    $score = -1;
                }
                $redis->zAdd($this->getRedisQueueKey(QueueType::WORKING), $score, $message->toArray());

                return $message;
            }
            elseif ($timeout < 0)
            {
                return null;
            }
        } while (($useTime = (microtime(true) - $time)) < $timeout);

        return null;
    }

    /**
     * 删除一个消息.
     */
    public function delete(IMessage $message): bool
    {
        $redis = RedisManager::getInstance($this->redisPoolName);

        return $redis->sAdd($this->getRedisQueueKey('deleted'), '') > 0;
    }

    /**
     * 清空队列.
     *
     * @param int|int[]|null $queueType 清空哪个队列，默认为全部
     *
     * @return void
     */
    public function clear($queueType = null)
    {
        if (null === $queueType)
        {
            $queueTypes = QueueType::getValues();
        }
        else
        {
            $queueTypes = (array) $queueType;
        }
        foreach ($queueTypes as $queueType)
        {
            try
            {
                switch ($queueType)
                {
                    case QueueType::READY:
                        $this->consumer->getAMQPChannel()->queue_purge($this->queueName);
                        RedisManager::getInstance($this->redisPoolName)->del($this->getRedisQueueKey('deleted'));
                        break;
                    case QueueType::WORKING:
                        RedisManager::getInstance($this->redisPoolName)->del($this->getRedisQueueKey(QueueType::WORKING));
                        break;
                    case QueueType::FAIL:
                        $this->failConsumer->getAMQPChannel()->queue_purge($this->failQueueName);
                        break;
                    case QueueType::TIMEOUT:
                        $this->timeoutConsumer->getAMQPChannel()->queue_purge($this->timeoutQueueName);
                        break;
                    case QueueType::DELAY:
                        $this->delayPublisher->getAMQPChannel()->queue_purge($this->delayQueueName);
                        break;
                }
            }
            catch (\PhpAmqpLib\Exception\AMQPProtocolChannelException $e)
            {
            }
        }
    }

    /**
     * 将消息标记为成功
     *
     * @param \Imi\AMQP\Queue\QueueAMQPMessage $message
     *
     * @return void
     */
    public function success(IMessage $message)
    {
        // @phpstan-ignore-next-line
        $this->consumer->getAMQPChannel()->basic_ack($message->getAmqpMessage()->getAMQPMessage()->getDeliveryTag());
    }

    /**
     * 将消息标记为失败.
     *
     * @param \Imi\AMQP\Queue\QueueAMQPMessage $message
     *
     * @return void
     */
    public function fail(IMessage $message, bool $requeue = false)
    {
        if ($requeue)
        {
            // @phpstan-ignore-next-line
            $this->consumer->getAMQPChannel()->basic_nack($message->getAmqpMessage()->getAMQPMessage()->getDeliveryTag(), false, $requeue);

            return;
        }
        if ($this->supportFail)
        {
            $amqpMessage = $message->getAmqpMessage();
            $amqpMessage->setRoutingKey(self::ROUTING_FAIL);
            $this->failPublisher->publish($amqpMessage);
            // @phpstan-ignore-next-line
            $this->consumer->getAMQPChannel()->basic_ack($message->getAmqpMessage()->getAMQPMessage()->getDeliveryTag());
        }
        else
        {
            // @phpstan-ignore-next-line
            $this->consumer->getAMQPChannel()->basic_nack($message->getAmqpMessage()->getAMQPMessage()->getDeliveryTag());
        }
    }

    /**
     * 获取队列状态
     */
    public function status(): QueueStatus
    {
        $status = [];
        $redis = RedisManager::getInstance($this->redisPoolName);

        // ready
        try
        {
            $result = $this->consumer->getAMQPChannel()->queue_declare($this->queueName, true, false, false, false);
            if (!$result)
            {
                throw new \RuntimeException(sprintf('Get queue:%s info failed', $this->queueName));
            }
            [, $ready, $unacked] = $result;
        }
        catch (\PhpAmqpLib\Exception\AMQPProtocolChannelException $e)
        {
            $ready = $unacked = 0;
        }
        $status['ready'] = $ready + $unacked;

        // working
        $status['working'] = $redis->zCard($this->getRedisQueueKey(QueueType::WORKING));

        // fail
        $fail = $unacked;
        try
        {
            if ($this->supportFail)
            {
                $result = $this->consumer->getAMQPChannel()->queue_declare($this->failQueueName, true, false, false, false);
                if (!$result)
                {
                    throw new \RuntimeException(sprintf('Get queue:%s info failed', $this->failQueueName));
                }
                [, $failReady, ] = $result;
                $fail += $failReady;
            }
        }
        catch (\PhpAmqpLib\Exception\AMQPProtocolChannelException $e)
        {
        }
        $status['fail'] = $fail;
        // timeout
        if ($this->supportTimeout)
        {
            try
            {
                $result = $this->consumer->getAMQPChannel()->queue_declare($this->timeoutQueueName, true, false, false, false);
                if (!$result)
                {
                    throw new \RuntimeException(sprintf('Get queue:%s info failed', $this->timeoutQueueName));
                }
                [, $timeoutReady, ] = $result;
                $status['timeout'] = $timeoutReady;
            }
            catch (\PhpAmqpLib\Exception\AMQPProtocolChannelException $e)
            {
                $status['timeout'] = 0;
            }
        }
        else
        {
            $status['timeout'] = 0;
        }

        // delay
        try
        {
            $result = $this->consumer->getAMQPChannel()->queue_declare($this->delayQueueName, true, false, false, false);
            if (!$result)
            {
                throw new \RuntimeException(sprintf('Get queue:%s info failed', $this->delayQueueName));
            }
            [, $delayReady, ] = $result;
        }
        catch (\PhpAmqpLib\Exception\AMQPProtocolChannelException $e)
        {
            $delayReady = 0;
        }
        $status['delay'] = $delayReady;

        return new QueueStatus($status);
    }

    /**
     * 将失败消息恢复到队列.
     *
     * 返回恢复数量
     */
    public function restoreFailMessages(): int
    {
        $count = 0;
        while ($message = $this->failConsumer->pop(0.001))
        {
            $amqpMessage = new \Imi\AMQP\Message();
            $amqpMessage->setBody($message->getBody());
            $amqpMessage->setRoutingKey(self::ROUTING_NORMAL);
            $this->publisher->publish($amqpMessage);
            // @phpstan-ignore-next-line
            $this->failConsumer->getAMQPChannel()->basic_ack($message->getAMQPMessage()->getDeliveryTag());
            ++$count;
        }
        $this->failConsumer->reopen();

        return $count;
    }

    /**
     * 将超时消息恢复到队列.
     *
     * 返回恢复数量
     */
    public function restoreTimeoutMessages(): int
    {
        $count = 0;
        while ($message = $this->timeoutConsumer->pop(0.001))
        {
            $amqpMessage = new \Imi\AMQP\Message();
            $amqpMessage->setBody($message->getBody());
            $amqpMessage->setRoutingKey(self::ROUTING_NORMAL);
            $this->publisher->publish($amqpMessage);
            // @phpstan-ignore-next-line
            $this->timeoutConsumer->getAMQPChannel()->basic_ack($message->getAMQPMessage()->getDeliveryTag());
            ++$count;
        }
        $this->timeoutConsumer->reopen();

        return $count;
    }

    /**
     * 获取消息 ID 的键.
     */
    public function getRedisMessageIdKey(): string
    {
        return $this->redisPrefix . $this->name . ':message:id';
    }

    /**
     * 获取队列的键.
     *
     * @param int|string $queueType
     */
    public function getRedisQueueKey($queueType): string
    {
        return $this->redisPrefix . $this->name . ':' . strtolower(QueueType::getName($queueType) ?? $queueType);
    }

    /**
     * 将处理超时的消息加入到超时队列.
     *
     * 返回消息数量
     *
     * @return void
     */
    protected function parseTimeoutMessages(int $count = 100)
    {
        $redis = RedisManager::getInstance($this->redisPoolName);
        $result = $redis->evalEx(<<<LUA
-- 查询消息ID
local messages = redis.call('zrevrangebyscore', KEYS[1], ARGV[1], 0, 'limit', 0, ARGV[2])
local messageIdCount = table.getn(messages)
if 0 == messageIdCount then
    return 0
end
-- 从工作队列删除
redis.call('zrem', KEYS[1], unpack(messages))
return messages
LUA
        , [
            $this->getRedisQueueKey(QueueType::WORKING),
            microtime(true),
            $count,
        ], 1);

        if (false === $result)
        {
            if ('' == ($error = $redis->getLastError()))
            {
                throw new QueueException('Queue parseTimeoutMessages failed');
            }
            else
            {
                throw new QueueException('Queue parseTimeoutMessages failed, ' . $error);
            }
        }

        foreach ($result ?: [] as $message)
        {
            $amqpMessage = new \Imi\AMQP\Message();
            $amqpMessage->setBody($message);
            $amqpMessage->setRoutingKey(self::ROUTING_TIMEOUT);
            $this->timeoutPublisher->publish($amqpMessage);
        }
    }

    /**
     * 消息是否被删除.
     */
    protected function messageIsDeleted(string $messageId, bool $delete = true): bool
    {
        $redis = RedisManager::getInstance($this->redisPoolName);

        return $redis->evalEx(<<<LUA
local deletedKey = KEYS[1];
local messageId = ARGV[1];
local deleteRecord = ARGV[2];
if(deleteRecord)
then
    return redis.call('srem', deletedKey, messageId);
else
    return redis.call('sismember', deletedKey, messageId);
end
LUA
        , [
            $this->getRedisQueueKey('deleted'),
            $messageId,
            $delete,
        ], 1) > 0;
    }
}
