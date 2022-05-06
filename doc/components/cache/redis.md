# Redis Cache

[toc]

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
                'poolName'              =>  '连接池名称',
                'prefix'                =>  '', // 缓存键前缀
                'formatHandlerClass'    =>  null, // 数据读写修改器
                'replaceDot'            =>  false, // 将 key 中的 "." 替换为 ":"
            ],
        ],
    ],
];
```

`formatHandlerClass` 可选：`Imi\Util\Format\Json`、`Imi\Util\Format\PhpSerialize`，或者可以自行实现`Imi\Util\Format\IFormat`接口
