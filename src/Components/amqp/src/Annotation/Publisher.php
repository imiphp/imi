<?php

declare(strict_types=1);

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 消费者.
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string|array      $queue      队列名称
 * @property string|array|null $exchange   交换机名称
 * @property string            $routingKey 路由键
 */
#[\Attribute]
class Publisher extends Base
{
    /**
     * @param string|array $queue
     * @param string|array $exchange
     */
    public function __construct(?array $__data = null, $queue = '', $exchange = null, string $routingKey = '')
    {
        parent::__construct(...\func_get_args());
    }
}
