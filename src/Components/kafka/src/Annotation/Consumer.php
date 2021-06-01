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
 */
class Consumer extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected ?string $defaultFieldName = 'topic';

    /**
     * 主题名称.
     *
     * @var string|string[]
     */
    public $topic;

    /**
     * 分组ID.
     *
     * @var string|null
     */
    public $groupId = null;

    /**
     * 连接池名称.
     *
     * @var string|null
     */
    public $poolName = null;
}
