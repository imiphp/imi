# 配置文件

配置文件分为：**项目配置**和**服务器配置**。

**项目配置** 是一些公共的配置，会影响整个项目。

**服务器配置** 是针对指定服务器，比如你写的某个 Http 服务的一些特别配置，可以在里面配置一些服务器相关的设置。

imi 还支持你在项目根目录下，建立一个`.env`文件，在里面设置运行环境配置。

## 配置文件结构

### 共有结构

```php
<?php
return [
    // 加载子配置文件，可以使用 \Imi\Config::get('@.别名1.xxx') 获取
    'configs'    =>    [
        "别名1"    =>    '配置文件路径1',
        "别名2"    =>    '配置文件路径2',
        ……
    ],

    // 如果配置了 configs.别名1，这里的值会被上面的文件覆盖
    '别名1' => [],

    // bean扫描目录，指定命名空间，建议省略
    // 'beanScan'	=>	[
    // 	'ImiDemo\WebSocketDemo\Listener',
    // ],

    // 日志配置，详见日志文档
    'logger' => [],

    // imi 核心配置，一般可以省略
    'imi' => [
        // 运行时缓存配置
        'runtime' => [
            // --- bean 相关缓存开始 ---
            'bean' => true, // 启用 bean 相关缓存，如果为false，则下面的配置无效
            'annotation_parser_data' => true, // 处理器存储数据缓存
            'annotation_parser_parsers' => true, // 处理器数据缓存
            'annotation_manager_annotations' => true, // 注解缓存
            'annotation_manager_annotation_relation' => true, // 注解关联缓存
            'partial' => true, // partial 缓存
            // --- bean 相关缓存结束 ---

            'cli' => true, // 命令行缓存
            'route' => true, // 路由缓存
            'enum' => true, // 枚举类缓存
            'event' => true, // 事件缓存

            // swoole 相关
            'swoole' => [
                'process' => true, // 进程缓存
            ],

            // workerman 相关
            'workerman' => [
                'process' => true, // 进程缓存
            ]
        ],
        'Timer' => '', // 定时器类名，详见定时器文档
        // 服务器容器绑定
        'beans' => [
            'ServerUtil' => '', // 服务器工具类类名，详见服务器工具类文档
            // 其它自定义
            'aaa' => XXX::class,
        ],
    ],
];
```

### Swoole 项目配置文件

```php
return [
    // 忽略扫描的命名空间
    'ignoreNamespace'   =>  [
        'Imi\Test\Component\Annotation\A\*',    // 忽略扫描该命名空间下所有类
        'Imi\Test\Component\Annotation\B\TestB',// 忽略该类
    ],
    // 全局忽略扫描的目录
    'ignorePaths' => [
        '绝对路径',
    ],
    // 仅扫描项目时忽略扫描的目录
    'appIgnorePaths' => [
        '绝对路径',
    ],
    // Swoole >= 4.1.0可用，不设置默认为true，开启一键协程化
    'enableCoroutine'    =>    true,
    // runtime目录设置，默认可不设置，为当前项目下的.runtime目录
    // 注意，多个项目不可设置为相同目录！
    'runtimePath'   =>  '/tmp/imidemo-runtime/',
    // 定义进程名规则
    'process'   =>  [
        'master'        =>  'imi:master:{namespace}',
        'manager'       =>  'imi:manager:{namespace}',
        'worker'        =>  'imi:worker-{workerId}:{namespace}',
        'taskWorker'    =>  'imi:taskWorker-{workerId}:{namespace}',
        'process'       =>  'imi:process-{processName}:{namespace}',
        'processPool'   =>  'imi:process-pool-{processPoolName}-{workerId}:{namespace}',
        'tool'          =>  'imi:{toolName}/{toolOperation}:{namespace}',
    ],
    // 主服务器配置
    'mainServer'	=>	[
        // 指定服务器命名空间
        'namespace'	=>	'ImiDemo\HttpDemo\MainServer',
        // 服务器类型(http/WebSocket/TcpServer/UdpServer)
        'type'		=>	\Imi\Swoole\Server\Type::HTTP,
        // 监听的IP地址，可选
        'host'		=>	'0.0.0.0',
        // 监听的端口
        'port'		=>	8080,
        // 参考 swoole mode，可选
        'mode'		=>	SWOOLE_BASE,
        // 参考 swoole sockType，可选
        'sockType'	=>	SWOOLE_SOCK_TCP,
        // 同步连接，当连接事件执行完后，才执行 receive 事件。仅 TCP 有效
        'syncConnect' => true,
        // 服务器配置，参数用法同\Swoole\Server->set($configs)
        // 参考: http://wiki.swoole.com/#/server/setting
        // 参考: http://wiki.swoole.com/#/websocket_server?id=%e9%80%89%e9%a1%b9
        // 参考: http://wiki.swoole.com/#/http_server?id=%e9%85%8d%e7%bd%ae%e9%80%89%e9%a1%b9
        'configs'	=>	[
            'reactor_num'	    => 8,
            'worker_num'	    => 8,
            'task_worker_num'	=> 16,
        ],
        // 服务器容器绑定
        'beans' => [
            'aaa' => XXX::class,
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

### Workerman 项目配置文件

```php
return [
    // 忽略扫描的命名空间
    'ignoreNamespace'   =>  [
        'Imi\Test\Component\Annotation\A\*',    // 忽略扫描该命名空间下所有类
        'Imi\Test\Component\Annotation\B\TestB',// 忽略该类
    ],
    // 全局忽略扫描的目录
    'ignorePaths' => [
        '绝对路径',
    ],
    // 仅扫描项目时忽略扫描的目录
    'appIgnorePaths' => [
        '绝对路径',
    ],
    // runtime目录设置，默认可不设置，为当前项目下的.runtime目录
    // 注意，多个项目不可设置为相同目录！
    'runtimePath'   =>  '/tmp/imidemo-runtime/',
    // Workerman 服务器配置
    'workermanServer' => [
        // 服务器名
        'http' => [
            // 指定服务器命名空间
            'namespace' => 'Imi\Workerman\Test\HttpServer\ApiServer',
            // 服务器类型
            'type'      => Imi\Workerman\Server\Type::HTTP, // HTTP、WEBSOCKET、TCP、UDP
            'host'      => '0.0.0.0',
            'port'      => 8080,
            // socket的上下文选项，参考：http://doc3.workerman.net/315128
            'context'   => [],
            'configs'   => [
                // 支持设置 Workerman 参数
            ],
            // 服务器容器绑定
            'beans' => [
                'aaa' => XXX::class,
            ],
        ],
    ],
];
```

### PHP-FPM 项目配置文件

```php
[
    // 忽略扫描的命名空间
    'ignoreNamespace'   =>  [
        'ImiApp\public\*', // 忽略 public 目录
        'Imi\Test\Component\Annotation\A\*',    // 忽略扫描该命名空间下所有类
        'Imi\Test\Component\Annotation\B\TestB',// 忽略该类
    ],
    // 全局忽略扫描的目录
    'ignorePaths' => [
        '绝对路径',
    ],
    // 仅扫描项目时忽略扫描的目录
    'appIgnorePaths' => [
        '绝对路径',
    ],
    'fpm' => [
        'serverPath' => '如果兼容 Swoole、Workerman 服务器子目录，则设置一下该目录路径',
    ],
]
```

### .env

在 `.env` 中的配置方式，支持两种写法。

写法一：直接注入配置，和`Config::set()`写法类似，支持`@app`等写法。

写法二：传统方式，如设定一个`ABC=123`，在配置文件中：

```php
return [
    'abc2'   =>  getenv('ABC'), // PHP 内置
    'abc1'   =>  imiGetEnv('ABC', 'default'), // imi 框架封装，支持第二个参数为默认值
];
```

如下，是设置连接池的uri例子：

```env
@app.pools.maindb.async.resource = "tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60"
```

数组的支持：

```env
@app.a.0.id = 1
@app.a.0.name = name1
@app.a.1.id = 2
@app.a.1.name = name2
```

同：

```php
[
    'a' =>  [
        ['id'=>1, 'name'=>'name1'],
        ['id'=>2, 'name'=>'name2'],
    ]
]
```
