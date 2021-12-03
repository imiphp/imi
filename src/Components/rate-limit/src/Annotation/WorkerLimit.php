<?php

declare(strict_types=1);

namespace Imi\RateLimit\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 并发工作数限制注解.
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @property string        $name     限流器名称
 * @property int           $max      最大同时工作数量
 * @property float|null    $timeout  工作超时时间，单位：秒，支持小数点精确到毫秒；默认为null，则不限制（不推荐不安全，有隐患）
 * @property callable|null $callback 触发限流的回调
 * @property string|null   $poolName 连接池名称，留空取默认 redis 连接池
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class WorkerLimit extends Base
{
    public function __construct(?array $__data = null, string $name = '', int $max = 0, ?float $timeout = null, ?callable $callback = null, ?string $poolName = null)
    {
        parent::__construct(...\func_get_args());
    }
}
