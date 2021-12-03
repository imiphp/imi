<?php

declare(strict_types=1);

namespace Imi\Kafka\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 消费者.
 *
 * @Annotation
 * @Target({"CLASS"})
 *
 * @property string|string[] $topic    主题名称
 * @property string|null     $groupId  分组ID
 * @property string|null     $poolName 连接池名称
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Consumer extends Base
{
    /**
     * {@inheritDoc}
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
