<?php

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Model\Enum\RedisStorageMode;

/**
 * Redis模型注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Model\Parser\ModelParser")
 */
class RedisEntity extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'poolName';

    /**
     * redis 连接池名称.
     *
     * @var string
     */
    public $poolName;

    /**
     * 第几个库，不传为null时使用连接池默认配置.
     *
     * @var int
     */
    public $db = null;

    /**
     * 键，支持定义多个参数，格式：{key}.
     *
     * @var string
     */
    public $key = '{key}';

    /**
     * hash 成员标识，支持定义多个参数，格式：{key}
     * 仅 hash 存储模式有效.
     *
     * @var string
     */
    public $member = '{member}';

    /**
     * 数据默认的过期时间，null为永不过期
     * hash 存储模式不支持过期
     *
     * @var int
     */
    public $ttl = null;

    /**
     * Redis 实体类存储模式.
     *
     * 支持 string、hash
     *
     * @var string
     */
    public $storage = RedisStorageMode::STRING;
}
