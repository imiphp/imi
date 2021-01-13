# 子服务器（单项目多端口多协议）

imi 原生支持一个项目，单次启动，监听多个端口多个协议。

比如你要做一个聊天系统，http 用于注册、登录、上传文件等等，WebSocket 用于即时通讯。

http 和 WebSocket 同时做在一个项目中，代码之间没有隔阂，可以方便地互相调用和复用。

## 子服务器配置

项目配置文件：

```php
[
    // 子服务器（端口监听）配置
    'subServers'    =>    [
        // 指定服务器命名空间
        'namespace'	=>	'ImiDemo\HttpDemo\MainServer',
        // 服务器类型(http/WebSocket/TcpServer/UdpServer)
        'type'		=>	\Imi\Swoole\Server\Type::HTTP,
        // 监听的IP地址，可选
        'host'		=>	'0.0.0.0',
        // 监听的端口
        'port'		=>	8080,
        // 参考 swoole sockType，可选
        'sockType'	=>	SWOOLE_SOCK_TCP,
        // 服务器配置，参数用法同\Swoole\Server->set($configs)
        'configs'	=>	[
        ],
    ],
]
```

## 子服务器命名空间配置

在子服务器命名空间所在目录，需要创建一个 `Main.php` 入口文件。

```php
<?php
namespace ImiApp\ApiServer;

use Imi\Main\BaseMain;

class Main extends BaseMain
{
    public function __init()
    {
        // 可以做一些初始化操作
    }

}
```

## 子服务器配置文件

文件：`config/config.php`

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
    	'ImiDemo\HttpDemo\MainServer\Controller',
    ],
];
```

> 只有在 `beanScan` 中配置，才可以被扫描到，注解才可以生效
