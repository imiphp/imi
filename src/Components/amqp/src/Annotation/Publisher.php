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
 */
class Publisher extends Base
{
    /**
     * 队列名称.
     *
     * @var string|array
     */
    public $queue = '';

    /**
     * 交换机名称.
     *
     * @var string|string[]
     */
    public $exchange;

    /**
     * 路由键.
     *
     * @var string
     */
    public $routingKey = '';
}
