<?php

declare(strict_types=1);

namespace Imi\Server\Http\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;

/**
 * 请求参数.
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @property string $name     导出数据名称；支持：$get.id、$post.content、$body.name
 * @property string $param    导出数据到的方法参数名
 * @property string $required 是否必选参数
 * @property mixed  $default  参数不存在时的默认值
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class RequestParam extends Base
{
    /**
     * @param mixed $default
     */
    public function __construct(?array $__data = null, string $name = '', string $param = '', bool $required = true, $default = null)
    {
        parent::__construct(...\func_get_args());
    }
}
