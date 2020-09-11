<?php

namespace Imi\Model\Enum;

/**
 * Redis 实体类存储模式.
 */
abstract class RedisStorageMode
{
    /**
     * 字符串模式.
     *
     * 使用 set/get 存序列化后的对象
     */
    const STRING = 'string';

    /**
     * hash 模式.
     *
     * 使用 hset/hget 存序列化后的对象
     */
    const HASH = 'hash';

    /**
     * hash 对象模式.
     *
     * 使用 hset/hget，将对象存到一个 key 中，member 为字段名
     */
    const HASH_OBJECT = 'hash_object';
}
