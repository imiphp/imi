<?php

declare(strict_types=1);

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 交换机.
 *
 * @Annotation
 * @Target({"CLASS"})
 *
 * @property string   $name       交换机名称
 * @property string   $type       类型；\PhpAmqpLib\Exchange\AMQPExchangeType::常量
 * @property bool     $passive    被动模式
 * @property bool     $durable    消息队列持久化
 * @property bool     $autoDelete 自动删除
 * @property bool     $internal   设置是否为rabbitmq内部使用, true表示是内部使用, false表示不是内部使用
 * @property bool     $nowait     是否非阻塞；true表示是。阻塞：表示创建交换器的请求发送后，阻塞等待RMQ Server返回信息。非阻塞：不会阻塞等待RMQ
 * @property array    $arguments  参数
 * @property int|null $ticket     参数
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Exchange extends Base
{
    public function __construct(?array $__data = null, string $name = '', string $type = \PhpAmqpLib\Exchange\AMQPExchangeType::DIRECT, bool $passive = false, bool $durable = true, bool $autoDelete = false, bool $internal = false, bool $nowait = false, array $arguments = [], ?int $ticket = null)
    {
        parent::__construct(...\func_get_args());
    }
}
