<?php

declare(strict_types=1);

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 队列.
 *
 * @Annotation
 * @Target({"CLASS"})
 *
 * @property string                           $name       队列名称
 * @property string                           $routingKey 路由键
 * @property bool                             $passive    被动模式
 * @property bool                             $durable    消息队列持久化
 * @property bool                             $exclusive  独占；如果是true，那么申明这个queue的connection断了，那么这个队列就被删除了，包括里面的消息。
 * @property bool                             $autoDelete 自动删除
 * @property bool                             $nowait     是否非阻塞；true表示是。阻塞：表示创建交换器的请求发送后，阻塞等待RMQ Server返回信息。非阻塞：不会阻塞等待RMQ
 * @property array|\PhpAmqpLib\Wire\AMQPTable $arguments  参数
 * @property int|null                         $ticket     参数
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Queue extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * @param array|\PhpAmqpLib\Wire\AMQPTable $arguments
     */
    public function __construct(?array $__data = null, string $name = '', string $routingKey = '', bool $passive = false, bool $durable = true, bool $exclusive = false, bool $autoDelete = false, bool $nowait = false, $arguments = [], ?int $ticket = null)
    {
        parent::__construct(...\func_get_args());
    }
}
