# Redis Cache

使用 redis 的 set 和 get 方法实现，支持 ttl

## 配置

```php
return [
    'caches'    =>    [
        // 缓存名称
        'alias1'    =>    [
            // 缓存驱动类
            'handlerClass'    =>    \Imi\Cache\Handler\Redis::class,
            // 驱动实例配置
            'option'        =>    [
                'poolName'    =>    '连接池名称',
                'formatHandlerClass'    =>    '', // 数据读写修改器
            ],
        ],
    ],
];
```

`formatHandlerClass` 可选：`Imi\Util\Format\Json`、`Imi\Util\Format\PhpSerialize`，或者可以自行实现`Imi\Util\Format\IFormat`接口
