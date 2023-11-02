<?php

declare(strict_types=1);

namespace Imi\Server\Http\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 请求参数.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class RequestParam extends Base
{
    public function __construct(
        /**
         * 导出数据名称；支持：$get.id、$post.content、$body.name.
         */
        public string $name = '',
        /**
         * 导出数据到的方法参数名.
         */
        public string $param = '',
        /**
         * 是否必选参数.
         */
        public bool $required = true,
        /**
         * 参数不存在时的默认值
         *
         * @var mixed
         */
        public $default = null
    ) {
    }
}
