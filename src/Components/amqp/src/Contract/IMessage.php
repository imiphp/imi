<?php

declare(strict_types=1);

namespace Imi\AMQP\Contract;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * 消息.
 */
interface IMessage
{
    /**
     * 获取主体内容.
     */
    public function getBody(): string;

    /**
     * 设置主体内容.
     *
     * @return static
     */
    public function setBody(string $body);

    /**
     * 设置主体数据.
     *
     * @param mixed $data
     *
     * @return self
     */
    public function setBodyData($data);

    /**
     * 获取主体数据.
     *
     * @return mixed
     */
    public function getBodyData();

    /**
     * Get 配置属性.
     */
    public function getProperties(): array;

    /**
     * Set 配置属性.
     *
     * @param array $properties 配置属性
     *
     * @return self
     */
    public function setProperties(array $properties);

    /**
     * Get 路由键.
     */
    public function getRoutingKey(): string;

    /**
     * Set 路由键.
     *
     * @param string $routingKey 路由键
     *
     * @return self
     */
    public function setRoutingKey(string $routingKey);

    /**
     * Get 当mandatory标志位设置为true时，如果exchange根据自身类型和消息routeKey无法找到一个符合条件的queue，那么会调用basic.return方法将消息返还给生产者；当mandatory设为false时，出现上述情形broker会直接将消息扔掉。
     */
    public function getMandatory(): bool;

    /**
     * Set 当mandatory标志位设置为true时，如果exchange根据自身类型和消息routeKey无法找到一个符合条件的queue，那么会调用basic.return方法将消息返还给生产者；当mandatory设为false时，出现上述情形broker会直接将消息扔掉。
     *
     * @return self
     */
    public function setMandatory(bool $mandatory);

    /**
     * Get 当immediate标志位设置为true时，如果exchange在将消息route到queue(s)时发现对应的queue上没有消费者，那么这条消息不会放入队列中。当与消息routeKey关联的所有queue(一个或多个)都没有消费者时，该消息会通过basic.return方法返还给生产者。
     */
    public function getImmediate(): bool;

    /**
     * Set 当immediate标志位设置为true时，如果exchange在将消息route到queue(s)时发现对应的queue上没有消费者，那么这条消息不会放入队列中。当与消息routeKey关联的所有queue(一个或多个)都没有消费者时，该消息会通过basic.return方法返还给生产者。
     *
     * @return self
     */
    public function setImmediate(bool $immediate);

    /**
     * Get ticket.
     */
    public function getTicket(): ?int;

    /**
     * Set ticket.
     *
     * @param int|null $ticket ticket
     *
     * @return self
     */
    public function setTicket(?int $ticket);

    /**
     * 设置 AMQP 消息.
     *
     * @return void
     */
    public function setAMQPMessage(AMQPMessage $amqpMessage);

    /**
     * 获取 AMQP 消息.
     *
     * @return \PhpAmqpLib\Message\AMQPMessage
     */
    public function getAMQPMessage(): ?AMQPMessage;
}
