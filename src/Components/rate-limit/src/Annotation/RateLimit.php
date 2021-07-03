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
 *
 * @property string        $name     限流器名称
 * @property int           $capacity 总容量
 * @property int           $fill     单位时间内生成填充的数量；不设置或为null时，默认值与 $capacity 相同
 * @property string        $unit     单位时间，默认为：秒(second)；支持：microsecond、millisecond、second、minute、hour、day、week、month、year
 * @property int           $deduct   每次扣除数量，默认为1
 * @property callable|null $callback 触发限流的回调
 * @property string|null   $poolName 连接池名称，留空取默认 redis 连接池
 */
#[\Attribute]
class RateLimit extends Base
{
    public function __construct(?array $__data = null, string $name = '', int $capacity = 0, int $fill = 0, string $unit = 'second', int $deduct = 1, ?callable $callback = null, ?string $poolName = null)
    {
        parent::__construct(...\func_get_args());
    }
}
