<?php

declare(strict_types=1);

namespace Imi\Cache\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;

/**
 * 缓存注解.
 *
 * 方法体执行后，将返回值存入缓存
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @property string|null $name       缓存器名称；为null则使用默认缓存器
 * @property string      $key        缓存键名；支持{id}、{data.name}形式，代入参数；支持{:args}代入所有方法参数的 hash 值；如果为空，则使用类名+方法名+全部参数，序列化后hash
 * @property string|null $value      写入缓存的值；默认为null时，返回值作为缓存的值；如果为字符串时（如：a.b），则将返回值作为数组或对象，取value->a->b下的值
 * @property int|null    $ttl        缓存超时时间，单位：秒
 * @property string      $hashMethod 可以指定 hash 方法，默认为：md5
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class CachePut extends Base
{
    public function __construct(?array $__data = null, ?string $name = null, string $key = '', ?string $value = null, ?int $ttl = null, string $hashMethod = 'md5')
    {
        parent::__construct(...\func_get_args());
    }
}
