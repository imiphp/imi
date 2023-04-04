# 请求上下文缓存

[toc]

缓存数据存储在请求上下文里。

## 配置

```php
<?php
return [
    'caches'    =>    [
        // 缓存名称
        'alias1'    =>    [
            // 缓存驱动类
            'handlerClass'    =>    \Imi\Cache\Handler\RequestContext::class,
            // 驱动实例配置
            'option'        =>    [
                'key' => 'RequestContextCache', // 在请求上下文中的键名
            ],
        ],
    ],
];
```

> 本驱动不支持也没有必要支持序列化
