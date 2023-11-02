<?php

declare(strict_types=1);

namespace Imi\RateLimit\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 限流器注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class RateLimit extends Base
{
    public function __construct(
        /**
         * 限流器名称.
         */
        public string $name = '',
        /**
         * 总容量.
         */
        public int $capacity = 0,
        /**
         * 单位时间内生成填充的数量；不设置或为null时，默认值与 $capacity 相同.
         */
        public ?int $fill = null,
        /**
         * 单位时间，默认为：秒(second)；支持：microsecond、millisecond、second、minute、hour、day、week、month、year.
         */
        public string $unit = 'second',
        /**
         * 每次扣除数量，默认为1.
         */
        public int $deduct = 1,
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
