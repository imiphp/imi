# RedisLock

RedisLock 是支持分布式的锁。

使用前需要配置 Redis 进程池。

类：`Imi\Lock\Handler\Redis`

Bean 名：`RedisLock`

## 可配置参数

```php
/**
 * 锁的唯一 ID
 *
 * @var string
 */
protected $id;

/**
 * 等待锁超时时间，单位：毫秒，0为不限制
 * 
 * @var int
 */
protected $waitTimeout = 3000;

/**
 * 锁超时时间，单位：毫秒
 * 
 * @var int
 */
protected $lockExpire = 3000;

/**
 * Redis 连接池名称
 *
 * @var string
 */
public $poolName;

/**
 * Redis 几号库
 *
 * @var integer
 */
public $db = 0;

/**
 * 获得锁每次尝试间隔，单位：毫秒
 * 
 * @var int
 */
public $waitSleepTime = 20;

/**
 * Redis key
 *
 * @var string
 */
public $key;

/**
 * Redis key 前置
 *
 * @var string
 */
public $keyPrefix = 'imi:lock:';

```

## 配置示例

```php
// 锁
'lock'  =>[
    'list'  =>  [
        'redis' =>  [
            'class' =>  'RedisLock',
            'options'   =>  [
                'poolName'  =>  'redis_test',
            ],
        ],
    ],
],
```
