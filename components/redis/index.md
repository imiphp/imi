# Redis

Redis 连接池配置方式已经在连接池里讲过，这里就不重复了，直接说使用方法。

## 连接池配置

```php
<?php
return [
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

## 基本操作

与原生 Redis 类操作方式基本一致，这里无需多说。

## 获取对象

```php
use \Imi\Redis\RedisManager;
$redis = RedisManager::getInstance();
$redis->set('imi:redis:test', date('Y-m-d H:i:s'));
$datetime = $redis->get('imi:redis:test');
```

## 获取新对象

每次调用都尝试从连接池中获取新的对象，要注意每次用完需要手动释放！

```php
use \Imi\Redis\RedisManager;
$redis = RedisManager::getNewInstance();
// $redis = RedisManager::getNewInstance($poolName);
RedisManager::release($redis);
```

## 获取默认连接池名称

```php
use \Imi\Redis\RedisManager;
echo RedisManager::getDefaultPoolName();
```

## 便捷操作

```php
use \Imi\Redis\Redis;
Redis::set('imi:redis:test', date('Y-m-d H:i:s'));
$datetime = Redis::get('imi:redis:test');
```

## 延迟收包

延迟收包的概念请查阅 Swoole 官方文档：https://wiki.swoole.com/wiki/page/587.html

### 延迟收包示例

如下代码所示，由于Redis速度非常快很难看出性能的差距，可以参考数据库中的延迟收包示例。

```php
$time = microtime(true);

$res1 = PoolManager::getResource('redis');
$res2 = PoolManager::getResource('redis');

$db1 = $res1->getInstance();
$db2 = $res2->getInstance();

$defer1 = $db1->deferGet('imi:redis:test');
$defer2 = $db2->deferGet('imi:redis:test');

var_dump(microtime(true) - $time);
```

批量执行收包：

```php
$time = microtime(true);

$res1 = PoolManager::getResource('redis');
$res2 = PoolManager::getResource('redis');

$db1 = $res1->getInstance();
$db2 = $res2->getInstance();

$defer1 = $db1->deferGet('imi:redis:test');
$defer2 = $db2->deferGet('imi:redis:test');

list($result1, $result2) = MuiltDefer::call($defer1, $defer2);

var_dump(microtime(true) - $time);
```