# 配置文件

在每个`Main`所在目录下的`config/config.php`配置文件，会在该`Main`被实例化时加载。

如果你不知道`Main`是什么，请看上一章：[《开始一个新项目》](/base/new.html)

imi 还支持你在项目根目录下，建立一个`.env`文件，在里面设置运行环境配置。

## 配置文件结构

### 共有结构

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
];
```

### 项目配置文件

```php
return [
    // 忽略扫描的命名空间
    'ignoreNamespace'   =>  [
        'Imi\Test\Component\Annotation\A\*',    // 忽略扫描该命名空间下所有类
        'Imi\Test\Component\Annotation\B\TestB',// 忽略该类
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
    'imi'   =>  [
        // 覆盖 imi 框架默认的 beanScan，可以禁用某些用不到的模块
        'beanScan'  =>  [
            'Imi\Config',
            'Imi\Bean',
            'Imi\Aop',
            'Imi\Annotation',
            'Imi\Cache',
            'Imi\Server',
            'Imi\Log',
            'Imi\Pool',
            'Imi\Db',
            'Imi\Redis',
            'Imi\Listener',
            'Imi\Model',
            'Imi\Task',
            'Imi\Tool',
            'Imi\Process',
            'Imi\HotUpdate',
            'Imi\Validate',
            'Imi\HttpValidate',
            'Imi\Enum',
            'Imi\Lock',
            'Imi\Facade',
        ],
    ],
    // 主服务器配置
    'mainServer'	=>	[
        // 指定服务器命名空间
        'namespace'	=>	'ImiDemo\HttpDemo\MainServer',
        // 服务器类型(http/WebSocket/TcpServer/UdpServer)
        'type'		=>	\Imi\Server\Type::HTTP,
        // 监听的IP地址，可选
        'host'		=>	'0.0.0.0',
        // 监听的端口
        'port'		=>	8080,
        // 参考 swoole mode，可选
        'mode'		=>	SWOOLE_BASE,
        // 参考 swoole sockType，可选
        'sockType'	=>	SWOOLE_SOCK_TCP,
        // 服务器配置，参数用法同\Swoole\Server->set($configs)
        'configs'	=>	[
            'reactor_num'	    => 8,
            'worker_num'	    => 8,
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

```
@app.pools.maindb.async.resource = "tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60"
```

数组的支持：

```
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
