 配置文件

在每个`Main`所在目录下的`config/config.php`配置文件，会在该`Main`被实例化时加载。

如果你不知道`Main`是什么，请看上一章：[《开始一个新项目》](/base/new.html)

## 配置文件结构

## 共有结构

```php
<?php
return [
    // 加载子配置文件，避免`config.php`过于臃肿不便维护
    // 要注意这里的别名不可与configs同级的名字重复，否则会被覆盖
    'configs'    =>    [
        "别名1"    =>    '配置文件路径1',
        "别名2"    =>    '配置文件路径2',
        ……
    ],
    // bean扫描目录，指定命名空间
    'beanScan'	=>	[
    	'ImiDemo\WebSocketDemo\Listener',
    ],
    // 配置方式注入类属性，可被注入的属性一般是public和protected
    'beans'    =>    [
        // 类名或类注解中定义的@Bean("名称")
        'hotUpdate'	=>	[
		// 'status'	=>	false, // 关闭热更新去除注释，不设置即为开启，建议生产环境关闭

		// --- 文件修改时间监控 ---
		// 'monitorClass'	=>	\Imi\HotUpdate\Monitor\FileMTime::class,
		// 'timespan'	=>	1, // 检测时间间隔，单位：秒

		// --- Inotify 扩展监控 ---
		// 'monitorClass'	=>	\Imi\HotUpdate\Monitor\Inotify::class,
		// 'timespan'	=>	0, // 检测时间间隔，单位：秒，使用扩展建议设为0性能更佳

		// 'includePaths'	=>	[], // 要包含的路径数组
		// 'excludePaths'	=>	[], // 要排除的路径数组，支持通配符*
		// 'defaultPath'	=>	[], // 设为数组则覆盖默认的监控路径
	],
    ],
    'db'	=>	[
    	// 数据库默认连接池名
    	'defaultPool'	=>	'maindb',
    ],
    // 连接池配置，详见对应章节
    'pools'	=>	[
    ],
    // 缓存配置，详见对应章节
    'caches'	=>	[
    ]
];
```

### 项目配置文件

```php
return [
    // Swoole >= 4.1.0可用，不设置默认为true，开启一键协程化
    // 'enableCoroutine'    =>    true,
    // runtime目录设置，默认可不设置，为临时目录
    // 'runtimePath'   =>  '/tmp/imidemo-runtime/',
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