<?php

declare(strict_types=1);

namespace Imi\AMQP\Queue;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Queue\Contract\IMessage;
use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Model\QueueStatus;
use Imi\RequestContext;
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
     */
    protected ?string $poolName = null;

    /**
     * 队列名称.
     */
    protected string $name = '';

    /**
     * 支持消息删除功能.
     *
     * 依赖 Redis
     */
    protected bool $supportDelete = true;

    /**
     * 支持消费超时队列功能.
     *
     * 依赖 Redis，并且自动增加一个队列
     */
    protected bool $supportTimeout = true;

    /**
     * 支持消费失败队列功能.
     *
     * 自动增加一个队列
     */
    protected bool $supportFail = true;

    /**
     * Redis 连接池名称.
     */
    protected ?string $redisPoolName = null;

    /**
     * Redis 键名前缀
     */
    protected string $redisPrefix = '';

    /**
     * 循环尝试 pop 的时间间隔，单位：秒.
     */
    protected float $timespan = 0.03;

    /**
     * 本地缓存的队列长度.
     */
    protected int $queueLength = 16;

    /**
     * 消息类名.
     */
    protected string $message = JsonAMQPMessage::class;

    /**
     * 构造方法的参数.
     */
    private array $args = [];

    public function __construct(string $name, array $config = [])
    {
        $this->name = $name;
        $this->traitConstruct($config);
        $this->args = \func_get_args();
    }

    public function __init(): void
    {
        $config = &$this->args[1];
        $config['poolName'] ??= $this->poolName;
        $config['name'] ??= $this->name;
        $config['supportDelete'] ??= $this->supportDelete;
        $config['supportTimeout'] ??= $this->supportTimeout;
        $config['supportFail'] ??= $this->supportFail;
        $config['redisPoolName'] ??= $this->redisPoolName;
        $config['redisPrefix'] ??= $this->redisPrefix;
        $config['timespan'] ??= $this->timespan;
        $config['queueLength'] ??= $this->queueLength;
        $config['message'] ??= $this->message;
    }

    protected function getHandler(): IQueueDriver
    {
        $context = RequestContext::getContext();

        return $context['QueueDriver.handler.' . $this->name] ??= BeanFactory::newInstance(AMQPQueueDriverHandler::class, ...$this->args);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function push(IMessage $message, float $delay = 0, array $options = []): string
    {
        return $this->getHandler()->push($message, $delay, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function pop(float $timeout = 0): ?IMessage
    {
        return $this->getHandler()->pop($timeout);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(IMessage $message): bool
    {
        return $this->getHandler()->delete($message);
    }

    /**
     * {@inheritDoc}
     */
    public function clear($queueType = null): void
    {
        $this->getHandler()->clear($queueType);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Imi\AMQP\Queue\QueueAMQPMessage $message
     */
    public function success(IMessage $message): int
    {
        return $this->getHandler()->success($message);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Imi\AMQP\Queue\QueueAMQPMessage $message
     */
    public function fail(IMessage $message, bool $requeue = false): int
    {
        return $this->getHandler()->fail($message, $requeue);
    }

    /**
     * {@inheritDoc}
     */
    public function status(): QueueStatus
    {
        return $this->getHandler()->status();
    }

    /**
     * {@inheritDoc}
     */
    public function restoreFailMessages(): int
    {
        return $this->getHandler()->restoreFailMessages();
    }

    /**
     * {@inheritDoc}
     */
    public function restoreTimeoutMessages(): int
    {
        return $this->getHandler()->restoreTimeoutMessages();
    }
}
