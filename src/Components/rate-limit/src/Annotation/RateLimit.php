<?php

declare(strict_types=1);

namespace Imi\RateLimit\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 限流器注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
class RateLimit extends Base
{
    /**
     * 限流器名称.
     *
     * @var string
     */
    public $name;

    /**
     * 总容量.
     *
     * @var int
     */
    public $capacity;

    /**
     * 单位时间内生成填充的数量.
     *
     * 不设置或为null时，默认值与 $capacity 相同
     *
     * @var int
     */
    public $fill;

    /**
     * 单位时间，默认为：秒(second).
     *
     * 支持：microsecond、millisecond、second、minute、hour、day、week、month、year
     *
     * @var string
     */
    public $unit = 'second';

    /**
     * 每次扣除数量，默认为1.
     *
     * @var int
     */
    public $deduct = 1;

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
