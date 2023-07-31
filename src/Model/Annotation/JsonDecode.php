<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Util\LazyArrayObject;

/**
 * JSON 反序列化时的配置.
 *
 * @Annotation
 *
 * @Target({"CLASS", "PROPERTY"})
 *
 * @property bool            $associative 是否返回关联数组。true-关联数组；false-对象
 * @property int             $depth       递归层数
 * @property int             $flags       json_decode() 的 flags 参数
 * @property string|callable $wrap        反序列化数据的包装，如果是对象或者数组时有效
 * @property bool            $arrayWrap   属性值类型为数组，使用 wrap 对数组成员进行包装
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
class JsonDecode extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'associative';

    /**
     * @param string|callable $wrap
     */
    public function __construct(?array $__data = null, bool $associative = true, int $depth = 512, int $flags = 0, $wrap = LazyArrayObject::class, bool $arrayWrap = false)
    {
        parent::__construct(...\func_get_args());
    }
}
