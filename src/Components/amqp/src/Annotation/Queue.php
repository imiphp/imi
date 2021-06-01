<?php

declare(strict_types=1);

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 队列.
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class Queue extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 队列名称.
     *
     * @var string
     */
    public $name = '';

    /**
     * 路由键.
     *
     * @var string
     */
    public $routingKey = '';

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
     * 独占
     * 如果是true，那么申明这个queue的connection断了，那么这个队列就被删除了，包括里面的消息。
     *
     * @var bool
     */
    public $exclusive = false;

    /**
     * 自动删除.
     *
     * @var bool
     */
    public $autoDelete = false;

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
     * @var array|\PhpAmqpLib\Wire\AMQPTable
     */
    public $arguments = [];

    /**
     * ticket.
     *
     * @var int|null
     */
    public $ticket = null;
}
