# 会话数据

imi 中长连接服务（Http2、WebSocket、TCP）中使用 `Imi\ConnectionContext` 类对连接的会话数据进行管理。在整个连接的生命周期中都有效。

比如我们可以在客户端发送认证鉴权操作时，向连接上下文中写入当前客户端的id等信息。

会话数据可以设置存储器，用于将会话数据保存在不同的地方，满足各种需求。

## 常见使用

```php
use Imi\ConnectionContext;

// 取值
echo ConnectionContext::get('name');
echo ConnectionContext::get('name', '默认值');

// 赋值
ConnectionContext::set('name', 'value');

// 获取所有数据
$array = ConnectionContext::getContext();

// 使用回调并且自动加锁进行操作，回调用返回数据会保存进连接上下文
ConnectionContext::use(function($data){
    // 本方法体会在锁中执行
    var_dump($data); // 读取数据
    $data['aaa'] = 222;
    return $data; // 写入数据，不return也可以，就是不修改
});
```

## 方法列表

```php
/**
 * 为当前连接创建上下文.
 */
public static function create(array $data = []): void;

/**
 * 从某个连接上下文中，加载到当前上下文或指定上下文中.
 */
public static function load(int $fromClientId, ?int $toClientId = null): void;

/**
 * 销毁当前连接的上下文.
 *
 * @param int|string|null $clientId
 */
public static function destroy($clientId = null): void;

/**
 * 判断当前连接上下文是否存在.
 *
 * @param int|string|null $clientId
 */
public static function exists($clientId = null): bool;

/**
 * 获取上下文数据.
 *
 * @param mixed           $default
 * @param int|string|null $clientId
 *
 * @return mixed
 */
public static function get(?string $name = null, $default = null, $clientId = null);

/**
 * 设置上下文数据.
 *
 * @param string          $name
 * @param mixed           $value
 * @param int|string|null $clientId
 */
public static function set(?string $name, $value, $clientId = null): void;

/**
 * 批量设置上下文数据.
 *
 * @param int|string|null $clientId
 */
public static function muiltiSet(array $data, $clientId = null): void;

/**
 * 使用回调并且自动加锁进行操作，回调用返回数据会保存进连接上下文.
 *
 * @param int|string|null $clientId
 */
public static function use(callable $callable, $clientId = null): void;

/**
 * 获取当前上下文.
 *
 * @param int|string|null $clientId
 */
public static function getContext($clientId = null): array;

/**
 * 绑定一个标记到当前连接.
 *
 * @param int|string|null $clientId
 */
public static function bind(string $flag, $clientId = null): void;

/**
 * 绑定一个标记到当前连接，如果已绑定返回false.
 *
 * @param int|string|null $clientId
 */
public static function bindNx(string $flag, $clientId = null): bool;

/**
 * 取消绑定.
 *
 * @param int|string $clientId
 * @param int|null   $keepTime 旧数据保持时间，null 则不保留
 */
public static function unbind(string $flag, $clientId, ?int $keepTime = null): void;

/**
 * 使用标记获取连接编号.
 *
 * @return array
 */
public static function getClientIdByFlag(string $flag);

/**
 * 使用标记获取连接编号.
 *
 * @param string[] $flags
 */
public static function getClientIdsByFlags(array $flags): array;

/**
 * 使用连接编号获取标记.
 *
 * @param int|string $clientId
 */
public static function getFlagByClientId($clientId): ?string;

/**
 * 使用连接编号获取标记.
 *
 * @param int[]|string[] $clientIds
 *
 * @return string[]
 */
public static function getFlagsByClientIds(array $clientIds): array;

/**
 * 使用标记获取旧的连接编号.
 */
public static function getOldClientIdByFlag(string $flag): ?int;

/**
 * 恢复标记对应连接中的数据.
 */
public static function restore(string $flag, ?int $toClientId = null): void;

/**
 * 获取当前连接号.
 *
 * @return int|string|null
 */
public static function getClientId();
    
```

## 会话存储器

### Local 本地变量存储器

本地变量存储，性能最高，适用于单机部署场景。

> 如果使用 Swoole 模式，只建议在 `SWOOLE_BASE` 模式下使用

```php
'beans' =>  [
    // 连接绑定器
    'ConnectionBinder'  =>  [
        // 存储器类，默认 ConnectionBinderRedis
        // 支持：ConnectionBinderRedis、ConnectionBinderLocal
        // 'handlerClass' => 'ConnectionBinderRedis',
    ],
    'ConnectionContextStore'   =>  [
        'handlerClass'  =>  \Imi\Server\ConnectionContext\StoreHandler\Local::class,
    ],
    'ConnectionContextLocal'    =>    [
        'lockId'    =>  null, // 非必设，可以用锁来防止数据错乱问题
    ],
],
```

### Redis 存储器

数据储存在 Redis 中，需要将多实例服务的 key 设为不同，防止冲突。

```php
'beans' =>  [
    // 连接绑定器
    'ConnectionBinder'  =>  [
        // 存储器类，默认 ConnectionBinderRedis
        // 支持：ConnectionBinderRedis、ConnectionBinderLocal
        // 'handlerClass' => 'ConnectionBinderRedis',
    ],
    'ConnectionContextStore'   =>  [
        'handlerClass'  =>  \Imi\Server\ConnectionContext\StoreHandler\Redis::class,
    ],
    'ConnectionContextRedis'    =>    [
        'redisPool'    => 'redis', // Redis 连接池名称
        'redisDb'      => 0, // redis中第几个库
        'key'          => 'imi:connect_context', // 键
        'heartbeatTimespan' => 5, // 心跳时间，单位：秒
        'heartbeatTtl' => 8, // 心跳数据过期时间，单位：秒
        'dataEncode'=>  'serialize', // 数据写入前编码回调
        'dataDecode'=>  'unserialize', // 数据读出后处理回调
        'lockId'    =>  null, // 非必设，可以用锁来防止数据错乱问题
    ],
],
```

### Swoole MemoryTable 存储器

数据储存在 Swoole 内存表中，适用于单机部署场景。

> 此方案只为解决《我可以不用，但你不能没有》的问题，除非你真的很了解 Swoole Table，否则千万别用。

```php
'beans' =>  [
    // 连接绑定器
    'ConnectionBinder'  =>  [
        // 存储器类，默认 ConnectionBinderRedis
        // 支持：ConnectionBinderRedis、ConnectionBinderLocal
        // 'handlerClass' => 'ConnectionBinderRedis',
    ],
    'ConnectionContextStore'   =>  [
        'handlerClass'  =>  \Imi\Swoole\Server\ConnectionContext\StoreHandler\MemoryTable::class,
    ],
    'ConnectionContextMemoryTable' =>  [
        'tableName' =>  'ConnectionContext', // tableName 你需要实现定义 MemoryTable，请查看相关章节
        'dataEncode'=>  'serialize', // 数据写入前编码回调
        'dataDecode'=>  'unserialize', // 数据读出后处理回调
        'lockId'    =>  null, // 非必设，因为如果用 MemoryTable，默认是用 MemoryTable 的 Lock
    ],
],
```

### Workerman Gateway 连接上下文处理器

适用于使用 Workerman Gateway 模式，支持分布式。

> 在 Swoole 中使用 Workerman Gateway 模式也可以用

```php
'beans' =>  [
    // 连接绑定器
    'ConnectionBinder'  =>  [
        // 存储器类，默认 ConnectionBinderRedis
        // 支持：ConnectionBinderRedis、ConnectionBinderLocal
        // 'handlerClass' => 'ConnectionBinderRedis',
    ],
    'ConnectionContextStore'   =>  [
        'handlerClass'  =>  \Imi\WorkermanGateway\Server\ConnectionContext\StoreHandler\ConnectionContextGateway::class,
    ],
    'ConnectionContextGateway' =>  [
        
    ],
],
```
