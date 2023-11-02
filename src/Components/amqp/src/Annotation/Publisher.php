<?php

declare(strict_types=1);

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 消费者.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Publisher extends Base
{
    public function __construct(
        /**
         * 队列名称.
         *
         * @var string|array
         */
        public $queue = '',
        /**
         * 交换机名称.
         *
         * @var string|array|null
         */
        public $exchange = null,
        /**
         * 路由键.
         */
        public string $routingKey = ''
    ) {
    }
}
