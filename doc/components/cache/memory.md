# 内存缓存

[toc]

缓存数据储存在全局存储对象里。

## 配置

```php
<?php
return [
    'caches'    =>    [
        // 缓存名称
        'alias1'    =>    [
            // 缓存驱动类
            'handlerClass'    =>    \Imi\Cache\Handler\Memory::class,
            // 驱动实例配置，暂无任何配置
            'option'        =>    [
            ],
        ],
    ],
];
```

> 本驱动不支持也没有必要支持序列化
