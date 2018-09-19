<?php
namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Redis模型注解
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Model\Parser\ModelParser")
 */
class RedisEntity extends Base
{
    /**
     * 只传一个参数时的参数名
     * @var string
     */
    protected $defaultFieldName = 'poolName';

    /**
     * redis 连接池名称
     * @var string
     */
    public $poolName;

    /**
     * 第几个库，不传为null时使用连接池默认配置
     * @var int
     */
    public $db = null;

    /**
     * 键，支持定义多个参数，格式：{key}
     * @var string
     */
    public $key = '{key}';

    /**
     * 数据默认的过期时间，null为永不过期
     * @var integer
     */
    public $ttl = null;
}