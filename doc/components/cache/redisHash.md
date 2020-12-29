# Redis Cache

使用 redis 的 hash，即 hset 和 hget 方法实现，不支持ttl

> 使用时需要注意，key格式为`hashkey->member`，即为`$redis->hget('hashkey', 'member')`

## 配置

```php
return [
    'caches'    =>    [
        // 缓存名称
        'alias1'    =>    [
            // 缓存驱动类
            'handlerClass'    =>    \Imi\Cache\Handler\RedisHash::class,
            // 驱动实例配置
            'option'        =>    [
                'poolName'    =>    '连接池名称',
                'separator' =>    '分隔符，分隔 hash key和 member，默认为->',
                'defaultHashKey'    =>    'imi:RedisHashCache', // 默认缺省的 hash key
                'formatHandlerClass'    =>    null, // 数据读写修改器
            ],
        ],
    ],
];
```

`formatHandlerClass` 可选：`Imi\Util\Format\Json`、`Imi\Util\Format\PhpSerialize`，或者可以自行实现`Imi\Util\Format\IFormat`接口
