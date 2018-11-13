# Redis

Redis 连接池配置方式已经在连接池里讲过，这里就不重复了，直接说使用方法。

## 基本操作

与原生 Redis 类操作方式基本一致，这里无需多说。

## 获取对象

```php
$redis = RedisManager::getInstance();
$redis->set('imi:redis:test', date('Y-m-d H:i:s'));
$datetime = $redis->get('imi:redis:test');
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