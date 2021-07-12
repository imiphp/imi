<?php

declare(strict_types=1);

namespace Imi\AMQP;

use Imi\AMQP\Contract\IMessage;
use Imi\App;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * AMQP 消息.
 */
class Message implements IMessage
{
    /**
     * 主体内容.
     *
     * @var mixed
     */
    protected $bodyData;

    /**
     * 配置属性.
     */
    protected array $properties = [
        'content_type'  => 'text/plain',
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
    ];

    /**
     * 路由键.
     */
    protected string $routingKey = '';

    /**
     * mandatory标志位
     * 当mandatory标志位设置为true时，如果exchange根据自身类型和消息routeKey无法找到一个符合条件的queue，那么会调用basic.return方法将消息返还给生产者；当mandatory设为false时，出现上述情形broker会直接将消息扔掉。
     */
    protected bool $mandatory = false;

    /**
     * immediate标志位
     * 当immediate标志位设置为true时，如果exchange在将消息route到queue(s)时发现对应的queue上没有消费者，那么这条消息不会放入队列中。当与消息routeKey关联的所有queue(一个或多个)都没有消费者时，该消息会通过basic.return方法返还给生产者。
     */
    protected bool $immediate = false;

    /**
     * ticket.
     */
    protected ?int $ticket = null;

    /**
     * 格式处理.
     */
    protected ?string $format = null;

    /**
     * AMQP 消息.
     */
    protected AMQPMessage $amqpMessage;

    public function __construct()
    {
    }

    /**
     * Get 配置属性.
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Set 配置属性.
     *
     * @param array $properties 配置属性
     *
     * @return self
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * Get 路由键.
     */
    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }

    /**
     * Set 路由键.
     *
     * @param string $routingKey 路由键
     */
    public function setRoutingKey(string $routingKey): self
    {
        $this->routingKey = $routingKey;

        return $this;
    }

    /**
     * Get 当mandatory标志位设置为true时，如果exchange根据自身类型和消息routeKey无法找到一个符合条件的queue，那么会调用basic.return方法将消息返还给生产者；当mandatory设为false时，出现上述情形broker会直接将消息扔掉。
     */
    public function getMandatory(): bool
    {
        return $this->mandatory;
    }

    /**
     * Set 当mandatory标志位设置为true时，如果exchange根据自身类型和消息routeKey无法找到一个符合条件的queue，那么会调用basic.return方法将消息返还给生产者；当mandatory设为false时，出现上述情形broker会直接将消息扔掉。
     */
    public function setMandatory(bool $mandatory): self
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * Get 当immediate标志位设置为true时，如果exchange在将消息route到queue(s)时发现对应的queue上没有消费者，那么这条消息不会放入队列中。当与消息routeKey关联的所有queue(一个或多个)都没有消费者时，该消息会通过basic.return方法返还给生产者。
     */
    public function getImmediate(): bool
    {
        return $this->immediate;
    }

    /**
     * Set 当immediate标志位设置为true时，如果exchange在将消息route到queue(s)时发现对应的queue上没有消费者，那么这条消息不会放入队列中。当与消息routeKey关联的所有queue(一个或多个)都没有消费者时，该消息会通过basic.return方法返还给生产者。
     */
    public function setImmediate(bool $immediate): self
    {
        $this->immediate = $immediate;

        return $this;
    }

    /**
     * Get ticket.
     */
    public function getTicket(): ?int
    {
        return $this->ticket;
    }

    /**
     * Set ticket.
     *
     * @param int|null $ticket ticket
     */
    public function setTicket(?int $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * 设置主体数据.
     *
     * @param mixed $data
     */
    public function setBodyData($data): self
    {
        $this->bodyData = $data;

        return $this;
    }

    /**
     * 获取主体数据.
     *
     * @return mixed
     */
    public function getBodyData()
    {
        return $this->bodyData;
    }

    /**
     * 获取主体内容.
     */
    public function getBody(): string
    {
        if (null === $this->format)
        {
            return $this->getBodyData();
        }
        else
        {
            /** @var \Imi\Util\Format\IFormat $formater */
            $formater = App::getBean($this->format);

            return $formater->encode($this->getBodyData());
        }
    }

    /**
     * 设置主体内容.
     *
     * @return static
     */
    public function setBody(string $body): self
    {
        if (null === $this->format)
        {
            $this->setBodyData($body);
        }
        else
        {
            /** @var \Imi\Util\Format\IFormat $formater */
            $formater = App::getBean($this->format);
            $data = $formater->decode($body);
            $this->setBodyData($data);
        }

        return $this;
    }

    /**
     * 设置 AMQP 消息.
     */
    public function setAMQPMessage(AMQPMessage $amqpMessage): void
    {
        $this->amqpMessage = $amqpMessage;
        $this->setBody($amqpMessage->getBody());
    }

    /**
     * 获取 AMQP 消息.
     *
     * @return \PhpAmqpLib\Message\AMQPMessage
     */
    public function getAMQPMessage(): ?AMQPMessage
    {
        return $this->amqpMessage;
    }
}
