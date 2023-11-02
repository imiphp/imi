<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 场景定义.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Scene extends Base
{
    public function __construct(
        /**
         * 场景名称.
         */
        public string $name = '',
        /**
         * 需要验证的字段名列表.
         */
        public array $fields = []
    ) {
    }
}
