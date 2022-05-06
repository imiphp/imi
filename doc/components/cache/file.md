# 文件缓存

[toc]

## 配置

```php
<?php
return [
    'caches'    =>    [
        // 缓存名称
        'alias1'    =>    [
            // 缓存驱动类
            'handlerClass'    =>    \Imi\Cache\Handler\File::class,
            // 驱动实例配置
            'option'        =>    [
                'savePath'    =>    '文件缓存保存路径',
                'formatHandlerClass'    =>    null, // 数据读写修改器
                // 保存文件名处理回调，一般可以不写
                // 'saveFileNameCallback'    =>    function($savePath, $key){
                //     return '';
                // },
                'keyAsFileName' => false, // 使用键名作为文件名，如果设置了 $saveFileNameCallback 则 $keyAsFileName 无效
            ],
        ],
    ],
];
```

`formatHandlerClass` 可选：`Imi\Util\Format\Json`、`Imi\Util\Format\PhpSerialize`，或者可以自行实现`Imi\Util\Format\IFormat`接口

> 文件缓存除非挂载 samba 等网络磁盘，否则不支持分布式存储，请慎重选择！
