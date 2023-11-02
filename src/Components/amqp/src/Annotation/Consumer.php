<?php

declare(strict_types=1);

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 消费者.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Consumer extends Base
{
    public function __construct(
        /**
         * 消费者标签.
         */
        public string $tag = '',
        /**
         * 队列名称.
         *
         * @var string|array
         */
        public $queue = '',
        /**
         * 交换机名称.
         *
         * @var string|string[]
         */
        public $exchange = null,
        /**
         * 路由键.
         */
        public string $routingKey = '',
        /**
         * 消息类名.
         */
        public string $message = \Imi\AMQP\Message::class,
        /**
         * mandatory标志位；当mandatory标志位设置为true时，如果exchange根据自身类型和消息routeKey无法找到一个符合条件的queue，那么会调用basic.return方法将消息返还给生产者；当mandatory设为false时，出现上述情形broker会直接将消息扔掉。
         */
        public bool $mandatory = false,
        /**
         * immediate标志位；当immediate标志位设置为true时，如果exchange在将消息route到queue(s)时发现对应的queue上没有消费者，那么这条消息不会放入队列中。当与消息routeKey关联的所有queue(一个或多个)都没有消费者时，该消息会通过basic.return方法返还给生产者。
         */
        public bool $immediate = false,
        public ?int $ticket = null
    ) {
    }
}
