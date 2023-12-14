<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Util\LazyArrayObject;

/**
 * JSON 反序列化时的配置.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
class JsonDecode extends Base
{
    public function __construct(
        /**
         * 是否返回关联数组。true-关联数组；false-对象
         */
        public bool $associative = true,
        /**
         * 递归层数.
         */
        public int $depth = 512,
        /**
         * json_decode() 的 flags 参数.
         */
        public int $flags = 0,
        /**
         * 反序列化数据的包装，如果是对象或者数组时有效.
         *
         * @var string|callable
         */
        public $wrap = LazyArrayObject::class,
        /**
         * 属性值类型为数组，使用 wrap 对数组成员进行包装.
         */
        public bool $arrayWrap = false
    ) {
    }
}
