<?php

declare(strict_types=1);

namespace Imi\RateLimit\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 阻塞等待消费.
 *
 * 当触发限流时，自动阻塞（协程挂起）等待
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class BlockingConsumer extends Base
{
    public function __construct(
        /**
         * 超时时间，单位：秒；为 null 不限制.
         */
        public ?int $timeout = null
    ) {
    }
}
