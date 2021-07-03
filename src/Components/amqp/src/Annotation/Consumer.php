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
 * @property string          $tag        消费者标签
 * @property string|array    $queue      队列名称
 * @property string|string[] $exchange   交换机名称
 * @property string          $routingKey 路由键
 * @property string          $message    消息类名
 * @property bool            $mandatory  mandatory标志位；当mandatory标志位设置为true时，如果exchange根据自身类型和消息routeKey无法找到一个符合条件的queue，那么会调用basic.return方法将消息返还给生产者；当mandatory设为false时，出现上述情形broker会直接将消息扔掉。
 * @property bool            $immediate  immediate标志位；当immediate标志位设置为true时，如果exchange在将消息route到queue(s)时发现对应的queue上没有消费者，那么这条消息不会放入队列中。当与消息routeKey关联的所有queue(一个或多个)都没有消费者时，该消息会通过basic.return方法返还给生产者。
 * @property int|null        $ticket
 */
#[\Attribute]
class Consumer extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected ?string $defaultFieldName = 'tag';

    /**
     * @param string|array    $queue
     * @param string|string[]|null $exchange
     */
    public function __construct(?array $__data = null, string $tag = '', $queue = '', $exchange = null, string $routingKey = '', string $message = \Imi\AMQP\Message::class, bool $mandatory = false, bool $immediate = false, ?int $ticket = null)
    {
        parent::__construct(...\func_get_args());
    }
}
