# Redis

Redis 连接池配置方式已经在连接池里讲过，这里就不重复了，直接说使用方法。

## 连接池配置

标准写法：

> 从 imi v1.2.1 版本开始支持

```php

<?php
return [
    'redis'	=>	[
        'defaultPool'	=>	'alias1', // 默认连接池
        'quickFromRequestContext'	=>	true, // 从当前上下文中获取公用连接
    ],
    'pools' => [
        // 连接池名称
        'alias1' => [
            'pool' => [
                // 同步池类名
                'syncClass'     =>    \Imi\Redis\SyncRedisPool::class,
                // 协程池类名
                'asyncClass'    =>    \Imi\Redis\CoroutineRedisPool::class,
                'config' => [
                    // 池子中最多资源数
                    // 'maxResources' => 10,
                    // 池子中最少资源数
                    // 'minResources' => 2,
                    // 资源回收时间间隔，单位：秒
                    // 'gcInterval' => 60,
                    // 获取资源最大存活时间，单位：秒
                    // 'maxActiveTime' => 3600,
                    // 等待资源最大超时时间，单位：毫秒
                    // 'waitTimeout' => 3000,
                    // 心跳时间间隔，单位：秒
                    // 'heartbeatInterval' => null,
                    // 当获取资源时，是否检查状态
                    // 'checkStateWhenGetResource' => true,
                    // 负载均衡-轮流
                    // 'resourceConfigMode' => ResourceConfigMode::TURN,
                    // 负载均衡-随机
                    // 'resourceConfigMode' => ResourceConfigMode::RANDOM,
                ],
            ],
            // 数组资源配置
            'resource' => [
                // 'host'	=>	'127.0.0.1',
                // 'port'	=>	6379,
                // 是否自动序列化变量
                // 'serialize'	=>	true,
                // 密码
                // 'password'	=>	null,
                // 第几个库
                // 'db'	=>	0,
            ],
            // uri资源配置，以分号;分隔多个，参数使用query参数格式，特殊字符需要转码
            // 'resource'  =>  'tcp://192.168.0.222&timeout=60;tcp://192.168.0.222',
        ],
    ],
];
```

旧写法：

> 依然支持，但不再推荐

```php
<?php
return [
    'redis'	=>	[
        'defaultPool'	=>	'alias1', // 默认连接池
        'quickFromRequestContext'	=>	true, // 从当前上下文中获取公用连接
    ],
    'pools' => [
        // 连接池名称
        'alias1' => [
            // 同步池子，task进程使用
            'sync' => [
                'pool' => [
                    'class'		=>	\Imi\Redis\SyncRedisPool::class,
                    'config' => [
                        // 池子中最多资源数
                        // 'maxResources' => 10,
                        // 池子中最少资源数
                        // 'minResources' => 2,
                        // 资源回收时间间隔，单位：秒
                        // 'gcInterval' => 60,
                        // 获取资源最大存活时间，单位：秒
                        // 'maxActiveTime' => 3600,
                        // 等待资源最大超时时间，单位：毫秒
                        // 'waitTimeout' => 3000,
                        // 心跳时间间隔，单位：秒
                        // 'heartbeatInterval' => null,
                        // 当获取资源时，是否检查状态
                        // 'checkStateWhenGetResource' => true,
                        // 负载均衡-轮流
                        // 'resourceConfigMode' => ResourceConfigMode::TURN,
                        // 负载均衡-随机
                        // 'resourceConfigMode' => ResourceConfigMode::RANDOM,
                    ],
                ],
                'resource' => [
                    // 'host'	=>	'127.0.0.1',
                    // 'port'	=>	6379,
                    // 是否自动序列化变量
                    // 'serialize'	=>	true,
                    // 密码
                    // 'password'	=>	null,
                    // 第几个库
                    // 'db'	=>	0,
                ],
            ],
            // 异步池子，worker进程使用
            'async' => [
                'pool'	=>	[
                    'class'		=>	\Imi\Redis\CoroutineRedisPool::class,
                    'config'	=>	[
                        // 同上
                    ],
                ],
                // uri方式，以分号;分隔多个，参数使用query参数格式，特殊字符需要转码
                'resource'  =>  'tcp://192.168.0.222&timeout=60;tcp://192.168.0.222',
            ],
        ]
    ],
];
```

## 基本使用

与原生 Redis 类操作方式基本一致，这里无需多说。

### 获取对象

```php
use \Imi\Redis\RedisManager;
$redis = RedisManager::getInstance();
$redis->set('imi:redis:test', date('Y-m-d H:i:s'));
$datetime = $redis->get('imi:redis:test');
```

### 获取新对象

每次调用都尝试从连接池中获取新的对象，要注意每次用完需要手动释放！

```php
use \Imi\Redis\RedisManager;
$redis = RedisManager::getNewInstance();
// $redis = RedisManager::getNewInstance($poolName);
RedisManager::release($redis);
```

### 获取默认连接池名称

```php
use \Imi\Redis\RedisManager;
echo RedisManager::getDefaultPoolName();
```

### 便捷操作

`Redis::方法名()`

```php
use \Imi\Redis\Redis;
Redis::set('imi:redis:test', date('Y-m-d H:i:s'));
$datetime = Redis::get('imi:redis:test');
```

### 回调方式使用Redis

```php
$result = Redis::use(function(\Imi\Redis\RedisHandler $redis){
    $redis->set('a', 1);
    return true;
}); // true
```

## 进阶使用

```php
$redis = RedisManager::getInstance();
$redis->方法名();
```

或

```php
\Imi\Redis\Redis::方法名();
```

### evalEx

imi 封装了一个基于 `evalSha` 和 `eval` 的便捷方法，优先使用 `evalSha` 尝试，失败则使用 `eval` 方法。

定义：`public function evalEx($script, $args = null, $num_keys = null)`

```php
return false !== $redis->evalEx(<<<SCRIPT
redis.call('set', 'a', '123')
return redis.call('get', 'a')
SCRIPT
    );
```

### scanEach

`scan` 方法的扩展简易遍历方法

参数与 `scan` 基本一致，无需传递 `it` 参数

```php
foreach(Redis::scanEach('imi:scanEach:*', 10) as $value)
{
    var_dump($value);
}
```

### hscanEach

`hscan` 方法的扩展简易遍历方法

参数与 `hscan` 基本一致，无需传递 `it` 参数

```php
foreach(Redis::hscanEach($key, 'value:*', 10) as $k => $value)
{
    var_dump($k, $value);
}
```

### sscanEach

`sscan` 方法的扩展简易遍历方法

参数与 `sscan` 基本一致，无需传递 `it` 参数

```php
foreach(Redis::sscanEach($key, '*', 10) as $value)
{
    var_dump($value);
}
```

### zscanEach

`zscan` 方法的扩展简易遍历方法

参数与 `zscan` 基本一致，无需传递 `it` 参数

```php
foreach(Redis::zscanEach($key, '*', 10) as $score)
{
    var_dump($value);
}
```
