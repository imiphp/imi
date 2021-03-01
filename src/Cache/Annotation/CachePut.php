<?php

declare(strict_types=1);

namespace Imi\Cache\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 缓存注解.
 *
 * 方法体执行后，将返回值存入缓存
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class CachePut extends Base
{
    /**
     * 缓存器名称
     * 为null则使用默认缓存器.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * 键名
     * 支持{id}、{data.name}形式，代入参数
     * 支持{:args}代入所有方法参数的 hash 值
     * 如果为null，则使用全部参数，序列化后hash.
     *
     * @var string
     */
    public string $key = '';

    /**
     * 写入缓存的值
     * 默认为null时，返回值作为缓存的值
     * 如果为字符串时（如：a.b），则将返回值作为数组或对象，取value->a->b下的值
     *
     * @var string|null
     */
    public ?string $value = null;

    /**
     * 缓存超时时间，单位：秒.
     *
     * @var int|null
     */
    public ?int $ttl = null;

    /**
     * 可以指定 hash 方法，默认为：md5.
     *
     * @var string
     */
    public string $hashMethod = 'md5';

    public function __construct(?array $__data = null, ?string $name = null, string $key = '', ?string $value = null, ?int $ttl = null, string $hashMethod = 'md5')
    {
        parent::__construct(...\func_get_args());
    }
}
