<?php

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 交换机.
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class Exchange extends Base
{
    /**
     * 交换机名称.
     *
     * @var string
     */
    public $name;

    /**
     * 类型.
     *
     * \PhpAmqpLib\Exchange\AMQPExchangeType::常量
     *
     * @var string
     */
    public $type = \PhpAmqpLib\Exchange\AMQPExchangeType::DIRECT;

    /**
     * 被动模式.
     *
     * @var bool
     */
    public $passive = false;

    /**
     * 消息队列持久化.
     *
     * @var bool
     */
    public $durable = true;

    /**
     * 自动删除.
     *
     * @var bool
     */
    public $autoDelete = false;

    /**
     * 设置是否为rabbitmq内部使用, true表示是内部使用, false表示不是内部使用.
     *
     * @var bool
     */
    public $internal = false;

    /**
     * 是否非阻塞
     * true表示是。阻塞：表示创建交换器的请求发送后，阻塞等待RMQ Server返回信息。非阻塞：不会阻塞等待RMQ.
     *
     * @var bool
     */
    public $nowait = false;

    /**
     * 参数.
     *
     * @var array
     */
    public $arguments = [];

    /**
     * ticket.
     *
     * @var int|null
     */
    public $ticket = null;
}
