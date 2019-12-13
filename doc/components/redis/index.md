# Redis

Redis 连接池配置方式已经在连接池里讲过，这里就不重复了，直接说使用方法。

## 连接池配置

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
                        // 同上
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

```php
use \Imi\Redis\Redis;
Redis::set('imi:redis:test', date('Y-m-d H:i:s'));
$datetime = Redis::get('imi:redis:test');
```

### 回调方式使用Redis

```php
$result = Redis::use(function($resource, $redis){
    redis->set('a', 1);
    return true;
}); // true
```

## 进阶使用

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
