<?php

declare(strict_types=1);

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 交换机.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Exchange extends Base
{
    public function __construct(
        /**
         * 交换机名称.
         */
        public string $name = '',
        /**
         * 类型；\PhpAmqpLib\Exchange\AMQPExchangeType::常量.
         */
        public string $type = \PhpAmqpLib\Exchange\AMQPExchangeType::DIRECT,
        /**
         * 被动模式.
         */
        public bool $passive = false,
        /**
         * 消息队列持久化.
         */
        public bool $durable = true,
        /**
         * 自动删除.
         */
        public bool $autoDelete = false,
        /**
         * 设置是否为rabbitmq内部使用, true表示是内部使用, false表示不是内部使用.
         */
        public bool $internal = false,
        /**
         * 是否非阻塞；true表示是。阻塞：表示创建交换器的请求发送后，阻塞等待RMQ Server返回信息。非阻塞：不会阻塞等待RMQ.
         */
        public bool $nowait = false,
        /**
         * 参数.
         */
        public array $arguments = [],
        /**
         * 参数.
         */
        public ?int $ticket = null
    ) {
    }
}
