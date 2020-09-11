<?php

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 批量设置序列化注解
 * 优先级低于针对属性单独设置的@Serializable注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Model\Parser\ModelParser")
 */
class Serializables extends Base
{
    /**
     * 模式
     * allow-白名单
     * deny-黑名单.
     *
     * @var string
     */
    public $mode;

    /**
     * 字段名数组.
     *
     * @var string[]
     */
    public $fields;
}
