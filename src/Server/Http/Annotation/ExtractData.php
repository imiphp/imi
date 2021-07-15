<?php

declare(strict_types=1);

namespace Imi\Server\Http\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 导出数据.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("\Imi\Bean\Parser\NullParser")
 *
 * @property string $name    导出数据名称；支持：$get.id、$post.content、$body.name
 * @property string $to      导出数据到的参数名
 * @property mixed  $default 参数不存在时的默认值
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class ExtractData extends Base
{
    /**
     * @param mixed $default
     */
    public function __construct(?array $__data = null, string $name = '', string $to = '', $default = null)
    {
        parent::__construct(...\func_get_args());
    }
}
