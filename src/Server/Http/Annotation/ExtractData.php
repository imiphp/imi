<?php

namespace Imi\Server\Http\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 导出数据.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("\Imi\Validate\Annotation\Parser\ValidateConditionParser")
 */
class ExtractData extends Base
{
    /**
     * 导出数据名称.
     *
     * 支持：
     * $get.id
     * $post.content
     * $body.name
     *
     * @var string
     */
    public $name;

    /**
     * 导出数据到的参数名.
     *
     * @var string
     */
    public $to;

    /**
     * 参数不存在时的默认值
     *
     * @var mixed
     */
    public $default;
}
