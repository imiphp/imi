<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 批量设置序列化注解
 * 优先级低于针对属性单独设置的@Serializable注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string $mode   模式；allow-白名单；deny-黑名单
 * @property array  $fields 字段名数组
 */
#[\Attribute]
class Serializables extends Base
{
    public function __construct(?array $__data = null, string $mode = '', array $fields = [])
    {
        parent::__construct(...\func_get_args());
    }
}
