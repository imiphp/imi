<?php

declare(strict_types=1);

namespace Imi\RateLimit\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 并发工作数限制注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class WorkerLimit extends Base
{
    public function __construct(
        /**
         * 限流器名称.
         */
        public string $name = '',
        /**
         * 最大同时工作数量.
         */
        public int $max = 0,
        /**
         * 工作超时时间，单位：秒，支持小数点精确到毫秒；默认为null，则不限制（不推荐不安全，有隐患）.
         */
        public ?float $timeout = null,
        /**
         * 触发限流的回调.
         *
         * @var ?callable
         */
        public $callback = null,
        /**
         * 连接池名称，留空取默认 redis 连接池.
         */
        public ?string $poolName = null
    ) {
    }
}
