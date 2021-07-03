<?php

declare(strict_types=1);

namespace Imi\Kafka\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 消费者.
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string|string[] $topic    主题名称
 * @property string|null     $groupId  分组ID
 * @property string|null     $poolName 连接池名称
 */
#[\Attribute]
class Consumer extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected ?string $defaultFieldName = 'topic';

    /**
     * @param string|string[] $topic
     */
    public function __construct(?array $__data = null, $topic = [], ?string $groupId = null, ?string $poolName = null)
    {
        parent::__construct(...\func_get_args());
    }
}
