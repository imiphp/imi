<?php

namespace Imi\Cache\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 缓存驱逐注解.
 *
 * 方法体执行时，将指定缓存清除
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Cache\Annotation\Parser\CacheParser")
 */
class CacheEvict extends Base
{
    /**
     * 缓存器名称
     * 为null则使用默认缓存器.
     *
     * @var string|null
     */
    public $name;

    /**
     * 键名
     * 支持{id}、{data.name}形式，代入参数
     * 支持{:args}代入所有方法参数的 hash 值
     * 如果为null，则使用全部参数，序列化后hash.
     *
     * @var string
     */
    public $key;

    /**
     * 在方法执行前删除缓存，默认为false.
     *
     * @var bool
     */
    public $beforeInvocation = false;

    /**
     * 可以指定 hash 方法，默认为：md5.
     *
     * @var string
     */
    public $hashMethod = 'md5';
}
