<?php

declare(strict_types=1);

namespace Imi\Server\Http\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;

/**
 * 导出数据.
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @property string $name    导出数据名称；支持：$get.id、$post.content、$body.name
 * @property string $to      导出数据到的参数名
 * @property mixed  $default 参数不存在时的默认值
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
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
