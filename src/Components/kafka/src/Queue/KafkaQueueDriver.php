<?php

declare(strict_types=1);

namespace Imi\Kafka\Queue;

use Imi\Bean\Annotation\Bean;
use Imi\Kafka\Pool\KafkaPool;
use Imi\Kafka\Queue\Contract\IKafkaPopMessage;
use Imi\Kafka\Queue\Contract\IKafkaPushMessage;
use Imi\Kafka\Queue\Model\KafkaPopMessage;
use Imi\Queue\Contract\IMessage;
use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Model\QueueStatus;
use Imi\Util\Traits\TDataToProperty;
use longlang\phpkafka\Consumer\Consumer;

/**
 * Kafka 队列驱动.
 *
 * @Bean("KafkaQueueDriver")
 */
class KafkaQueueDriver implements IQueueDriver
{
    use TDataToProperty{
        __construct as private traitConstruct;
    }

    /**
     * Kafka 连接池名称.
     */
    protected ?string $poolName;

    /**
     * 队列名称.
     */
    protected string $name;

    /**
     * 分组ID.
     */
    public ?string $groupId = null;

    /**
     * @var Consumer[]
     */
    private array $consumers;

    public function __construct(string $name, array $config = [])
    {
        $this->name = $name;
        $this->traitConstruct($config);
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
        if ($delay > 0)
        {
            throw new \RuntimeException('Unsupport delay push in KafkaQueueDriver');
        }
        $producer = KafkaPool::getInstance($this->poolName);
        if ($message instanceof IKafkaPushMessage)
        {
            $key = $message->getKey();
            $headers = $message->getHeaders();
            $partition = $message->getPartition();
            $brokerId = $message->getBrokerId();
        }
        $producer->send($this->name, $message->getMessage(), $key ?? null, $headers ?? [], $partition ?? null, $brokerId ?? null);

        return '';
    }

    /**
     * 从队列弹出一个消息.
     */
    public function pop(float $timeout = 0): ?IMessage
    {
        $consumer = $this->getConsumer($this->name);
        $consumeResult = $consumer->consume();
        if (!$consumeResult)
        {
            return null;
        }
        $message = new KafkaPopMessage();
        $message->setMessage($consumeResult->getValue());
        $message->setConsumeMessage($consumeResult);

        return $message;
    }

    /**
     * 删除一个消息.
     */
    public function delete(IMessage $message): bool
    {
        throw new \RuntimeException('Unsupport delete message in KafkaQueueDriver');
    }

    /**
     * 清空队列.
     *
     * @param int|int[]|null $queueType 清空哪个队列，默认为全部
     */
    public function clear($queueType = null): void
    {
        throw new \RuntimeException('Unsupport clear queue in KafkaQueueDriver');
    }

    /**
     * 将消息标记为成功
     */
    public function success(IMessage $message): void
    {
        if (!$message instanceof IKafkaPopMessage)
        {
            throw new \RuntimeException('KafkaQueueDriver::success() only support Imi\Kafka\Queue\Contract\IKafkaPopMessage');
        }
        $consumer = $this->getConsumer($this->name);
        $consumer->ack($message->getConsumeMessage());
    }

    /**
     * 将消息标记为失败.
     */
    public function fail(IMessage $message, bool $requeue = false): void
    {
    }

    /**
     * 获取队列状态
     */
    public function status(): QueueStatus
    {
        return new QueueStatus([]);
    }

    /**
     * 将失败消息恢复到队列.
     *
     * 返回恢复数量
     */
    public function restoreFailMessages(): int
    {
        return 0;
    }

    /**
     * 将超时消息恢复到队列.
     *
     * 返回恢复数量
     */
    public function restoreTimeoutMessages(): int
    {
        return 0;
    }

    public function close(): void
    {
        foreach ($this->consumers as $consumer)
        {
            $consumer->close();
        }
        $this->consumers = [];
    }

    protected function getConsumer(string $topic): Consumer
    {
        if (!isset($this->consumers[$topic]))
        {
            $config = [];
            if (null !== $this->groupId)
            {
                $config['groupId'] = $this->groupId;
            }

            return $this->consumers[$topic] = KafkaPool::createConsumer($this->poolName, $topic, $config);
        }

        return $this->consumers[$topic];
    }
}
