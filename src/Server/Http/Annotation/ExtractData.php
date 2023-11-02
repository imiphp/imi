<?php

declare(strict_types=1);

namespace Imi\Server\Http\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 导出数据.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ExtractData extends Base
{
    public function __construct(
        /**
         * 导出数据名称；支持：$get.id、$post.content、$body.name.
         */
        public string $name = '',
        /**
         * 导出数据到的参数名.
         */
        public string $to = '',
        /**
         * 参数不存在时的默认值
         *
         * @var mixed
         */
        public $default = null
    ) {
    }
}
