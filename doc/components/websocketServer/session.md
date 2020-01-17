# 会话数据

IMI 中 WebSocket 服务中使用 `Imi\ConnectContext` 类对连接的会话数据进行管理。在整个连接的生命周期中都有效。

## 使用

```php
use Imi\ConnectContext;

// 取值
echo ConnectContext::get('name');
echo ConnectContext::get('name', 'default value');

// 赋值
ConnectContext::set('name', 'value');

// 获取所有数据
$array = ConnectContext::getContext();

// 使用回调并且自动加锁进行操作，回调用返回数据会保存进连接上下文
ConnectContext::use(function($data){
    // 本方法体会在锁中执行
    var_dump($data); // 读取数据
    $data['aaa'] = 222;
    return $data; // 写入数据，不return也可以，就是不修改
});
```

## 配置

### ConnectContext 存储器

MemoryTable:

```php
'beans' =>  [
    'ConnectContextStore'   =>  [
        'handlerClass'  =>  \Imi\Server\ConnectContext\StoreHandler\MemoryTable::class,
    ],
    'ConnectContextMemoryTable' =>  [
        'tableName' =>  'connectContext', // tableName 你需要实现定义 MemoryTable，请查看相关章节
        'dataEncode'=>  'serialize', // 数据写入前编码回调
        'dataDecode'=>  'unserialize', // 数据读出后处理回调
        'lockId'    =>  null, // 非必设，因为如果用 MemoryTable，默认是用 MemoryTable 的 Lock
    ],
],
```

Redis:

```php
'beans' =>  [
    'ConnectContextStore'   =>  [
        'handlerClass'  =>  \Imi\Server\ConnectContext\StoreHandler\Redis::class,
    ],
    'ConnectContextRedis'    =>    [
        'redisPool'    => 'redis', // Redis 连接池名称
        'redisDb'      => 0, // redis中第几个库
        'key'          => 'imi:connect_context', // 键
        'heartbeatTimespan' => 5, // 心跳时间，单位：秒
        'heartbeatTtl' => 8, // 心跳数据过期时间，单位：秒
        'dataEncode'=>  'serialize', // 数据写入前编码回调
        'dataDecode'=>  'unserialize', // 数据读出后处理回调
        'lockId'    =>  null, // 必设，需要用锁来防止数据错乱问题
    ],
],
```

Local:

本地变量存储，性能最高

> 只建议在 `SWOOLE_BASE` 模式下使用

```php
'beans' =>  [
    'ConnectContextStore'   =>  [
        'handlerClass'  =>  \Imi\Server\ConnectContext\StoreHandler\Local::class,
    ],
    'ConnectContextLocal'    =>    [
        'lockId'    =>  null, // 必设，需要用锁来防止数据错乱问题
    ],
],
```
