<?php

declare(strict_types=1);

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 队列.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Queue extends Base
{
    public function __construct(
        /**
         * 队列名称.
         */
        public string $name = '',
        /**
         * 路由键.
         */
        public string $routingKey = '',
        /**
         * 被动模式.
         */
        public bool $passive = false,
        /**
         * 消息队列持久化.
         */
        public bool $durable = true,
        /**
         * 独占；如果是true，那么申明这个queue的connection断了，那么这个队列就被删除了，包括里面的消息。
         */
        public bool $exclusive = false,
        /**
         * 自动删除.
         */
        public bool $autoDelete = false,
        /**
         * 是否非阻塞；true表示是。阻塞：表示创建交换器的请求发送后，阻塞等待RMQ Server返回信息。非阻塞：不会阻塞等待RMQ.
         */
        public bool $nowait = false,
        /**
         * 参数.
         *
         * @var array|\PhpAmqpLib\Wire\AMQPTable
         */
        public $arguments = [],
        /**
         * 参数.
         */
        public ?int $ticket = null
    ) {
    }
}
