<?php

declare(strict_types=1);

namespace Imi\Kafka\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 消费者.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Consumer extends Base
{
    public function __construct(
        /**
         * 主题名称.
         *
         * @var string|string[]
         */
        public $topic = [],
        /**
         * 分组ID.
         */
        public ?string $groupId = null,
        /**
         * 连接池名称.
         */
        public ?string $poolName = null
    ) {
    }
}
