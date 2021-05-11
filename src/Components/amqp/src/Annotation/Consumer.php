<?php

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
class Consumer extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'tag';

    /**
     * 消费者标签.
     *
     * @var string
     */
    public $tag = '';

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

    /**
     * 消息类名.
     *
     * @var string
     */
    public $message = \Imi\AMQP\Message::class;

    /**
     * mandatory标志位
     * 当mandatory标志位设置为true时，如果exchange根据自身类型和消息routeKey无法找到一个符合条件的queue，那么会调用basic.return方法将消息返还给生产者；当mandatory设为false时，出现上述情形broker会直接将消息扔掉。
     *
     * @var bool
     */
    public $mandatory = false;

    /**
     * immediate标志位
     * 当immediate标志位设置为true时，如果exchange在将消息route到queue(s)时发现对应的queue上没有消费者，那么这条消息不会放入队列中。当与消息routeKey关联的所有queue(一个或多个)都没有消费者时，该消息会通过basic.return方法返还给生产者。
     *
     * @var bool
     */
    public $immediate = false;

    /**
     * ticket.
     *
     * @var int|null
     */
    public $ticket = null;
}
