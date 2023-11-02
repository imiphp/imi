<?php

declare(strict_types=1);

namespace Imi\Cache\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 缓存驱逐注解.
 *
 * 方法体执行时，将指定缓存清除
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class CacheEvict extends Base
{
    public function __construct(
        /**
         * 缓存器名称；为null则使用默认缓存器.
         */
        public ?string $name = null,
        /**
         * 缓存键名；支持{id}、{data.name}形式，代入参数；支持{:args}代入所有方法参数的 hash 值；如果为空，则使用类名+方法名+全部参数，序列化后hash.
         */
        public string $key = '',
        /**
         * 在方法执行前删除缓存，默认为false.
         */
        public bool $beforeInvocation = false,
        /**
         * 可以指定 hash 方法，默认为：md5.
         */
        public string $hashMethod = 'md5'
    ) {
    }
}
