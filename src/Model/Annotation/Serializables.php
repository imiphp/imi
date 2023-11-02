<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 批量设置序列化注解
 * 优先级低于针对属性单独设置的@Serializable注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Serializables extends Base
{
    public function __construct(
        /**
         * 模式；allow-白名单；deny-黑名单.
         */
        public string $mode = '',
        /**
         * 字段名数组.
         */
        public array $fields = []
    ) {
    }
}
