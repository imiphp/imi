<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Model\Enum\RedisStorageMode;

/**
 * Redis模型注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string|null $poolName redis 连接池名称
 * @property int|null    $db       第几个库，不传为null时使用连接池默认配置
 * @property string      $key      键，支持定义多个参数，格式：{key}
 * @property string      $member   redis hash 成员标识，支持定义多个参数，格式：{key}；仅 hash 存储模式有效
 * @property int|null    $ttl      数据默认的过期时间，null为永不过期；hash 存储模式不支持过期
 * @property string      $storage  Redis 实体类存储模式；支持 string、hash
 */
#[\Attribute]
class RedisEntity extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'poolName';

    public function __construct(?array $__data = null, ?string $poolName = null, ?int $db = null, string $key = '{key}', string $member = '{member}', ?int $ttl = null, string $storage = RedisStorageMode::STRING)
    {
        parent::__construct(...\func_get_args());
    }
}
