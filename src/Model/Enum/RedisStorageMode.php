<?php

declare(strict_types=1);

namespace Imi\Model\Enum;

/**
 * Redis 实体类存储模式.
 */
class RedisStorageMode
{
    /**
     * 字符串模式.
     *
     * 使用 set/get 存序列化后的对象
     */
    public const STRING = 'string';

    /**
     * hash 模式.
     *
     * 使用 hset/hget 存序列化后的对象
     */
    public const HASH = 'hash';

    /**
     * hash 对象模式.
     *
     * 使用 hset/hget，将对象存到一个 key 中，member 为字段名
     */
    public const HASH_OBJECT = 'hash_object';

    private function __construct()
    {
    }
}
