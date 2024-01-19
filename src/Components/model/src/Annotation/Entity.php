<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 实体注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Entity extends Base
{
    public function __construct(
        /**
         * 序列化时使用驼峰命名.
         */
        public bool $camel = true,
        /**
         * 模型对象是否作为 bean 类使用.
         */
        public bool $bean = true,
        /**
         * 是否启用增量更新，默认为 false.
         */
        public bool $incrUpdate = false
    ) {
    }
}
