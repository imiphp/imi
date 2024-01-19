<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Model\Enum\RedisStorageMode;

/**
 * Redis模型注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class RedisEntity extends Base
{
    public function __construct(
        /**
         * redis 连接池名称.
         */
        public ?string $poolName = null,
        /**
         * 第几个库，不传为null时使用连接池默认配置.
         */
        public ?int $db = null,
        /**
         * 键，支持定义多个参数，格式：{key}.
         */
        public string $key = '{key}',
        /**
         * redis hash 成员标识，支持定义多个参数，格式：{key}；仅 hash 存储模式有效.
         */
        public string $member = '{member}',
        /**
         * 数据默认的过期时间，null为永不过期；hash 存储模式不支持过期
         */
        public ?int $ttl = null,
        /**
         * Redis 实体类存储模式；支持 string、hash.
         */
        public string $storage = RedisStorageMode::STRING,
        /**
         * 格式.
         */
        public ?string $formatter = null
    ) {
    }
}
