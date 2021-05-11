<?php

namespace Imi\RateLimit\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 并发工作数限制注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
class WorkerLimit extends Base
{
    /**
     * 限流器名称.
     *
     * @var string
     */
    public $name;

    /**
     * 最大同时工作数量.
     *
     * @var int
     */
    public $max;

    /**
     * 工作超时时间，单位：秒，支持小数点精确到毫秒.
     *
     * 默认为null，则不限制（不推荐不安全，有隐患）
     *
     * @var float|null
     */
    public $timeout;

    /**
     * 触发限流的回调.
     *
     * @var callable
     */
    public $callback;

    /**
     * 连接池名称，留空取默认 redis 连接池.
     *
     * @var string|null
     */
    public $poolName;
}
