在每个`Main`所在目录下的`config/config.php`配置文件，会在该`Main`被实例化时加载。

如果你不知道`Main`是什么，请看上一章：[《开始一个新项目》](/base/new.html)

## 公共配置约定

```php
return [
    // 加载子配置文件，避免`config.php`过于臃肿不便维护
    'configs'    =>    [
        "别名1"    =>    '配置文件路径1',
        "别名2"    =>    '配置文件路径2',
        ……
    ],
];
```

## 项目配置文件

```php
return [
    // 主服务器配置
    'mainServer'	=>	[
        // 指定服务器命名空间
        'namespace'	=>	'ImiDemo\HttpDemo\MainServer',
        // 服务器类型，暂时仅支持Type::HTTP
        'type'		=>	Type::HTTP,
        // 监听的IP地址，可选
        // 'host'		=>	'0.0.0.0',
        // 监听的端口
        'port'		=>	8080,
        // 参考 swoole mode，可选
        // 'mode'		=>	SWOOLE_BASE,
        // 参考 swoole sockType，可选
        // 'sockType'	=>	SWOOLE_SOCK_TCP,
        // 服务器配置，参数用法同\Swoole\Server->set($configs)
        'configs'	=>	[
            'reactor_num'	=> 8,
            'worker_num'	=> 8,
            'task_worker_num'	=> 16,
        ],
    ],
    // 子服务器（端口监听）配置
    'subServers'    =>    [
        // 子服务器别名
        'alias1'	=>	[
            // 这里同主服务器配置
        ]
    ],
];
```