# Apcu 缓存

[toc]

使用需要安装 Apcu 扩展

> Apcu 仅推荐在 php-fpm 模式使用

## 配置

```php
<?php
return [
    'caches'    =>    [
        // 缓存名称
        'alias1'    =>    [
            // 缓存驱动类
            'handlerClass'    =>    \Imi\Cache\Handler\Apcu::class,
            // 驱动实例配置
            'option'        =>    [
                'prefix' =>  '', // 缓存键前缀
            ],
        ],
    ],
];
```
