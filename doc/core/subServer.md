# Swoole 子服务器（单项目多端口多协议）

[toc]

imi 原生支持一个项目，单次启动，监听多个端口多个协议。

比如你要做一个聊天系统，http 用于注册、登录、上传文件等等，WebSocket 用于即时通讯。

http 和 WebSocket 同时做在一个项目中，代码之间没有隔阂，可以方便地互相调用和复用。

这种方式还能大大减少服务器的启动数量，节约服务器资源。

## 子服务器配置

项目配置文件：

```php
[
    // 子服务器（端口监听）配置
    'subServers'    =>    [
        // 子服务器别名
        'alias1'    =>    [
            // 指定服务器命名空间
            'namespace'    =>    'ImiDemo\HttpDemo\MainServer',
            // 服务器类型(http/WebSocket/TcpServer/UdpServer)
            'type'        =>    \Imi\Swoole\Server\Type::HTTP,
            // 监听的IP地址，可选
            'host'        =>    '0.0.0.0',
            // 监听的端口
            'port'        =>    8080,
            // 参考 swoole sockType，可选
            'sockType'    =>    SWOOLE_SOCK_TCP,
            // 同步连接，当连接事件执行完后，才执行 receive 事件。仅 TCP、WebSocket 且 SWOOLE_BASE 模式有效
            'syncConnect' => true,
            'nonControlFrameType' => \Imi\Server\WebSocket\Enum\NonControlFrameType::TEXT, // 配置 WebSocket 纯文本通信协议
            // 'nonControlFrameType' => \Imi\Server\WebSocket\Enum\NonControlFrameType::BINARY, // 配置 WebSocket 二进制通信协议
            // 服务器配置，参数用法同\Swoole\Server->set($configs)
            'configs'    =>    [
            ],
            // 容器绑定
            'beans' => [
                'aaa' => XXX::class,
            ],
            // $request->getAppUri() 参数替换，每个参数都是可选项
            // 下面例子最终获取到的 Uri 为：https://root:123@imi-test:1234/test?id=666#test
            'appUri' => [
                'host'     => 'imi-test',   // 主机名
                'port'     => 1234,         // 端口
                'scheme'   => 'https',      // 协议
                'user'     => 'root',       // 用户名
                'pass'     => '123',        // 密码
                'path'     => '/test',      // 路径
                'query'    => 'id=666',     // 查询参数
                'fragment' => 'test',       // 锚点
            ],
            // 也支持回调
            'appUri' => function(\Imi\Util\Uri $uri) {
                return $uri->withHost('imi-test');
            },
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
    public function __init(): void
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
    // 加载子配置文件，可以使用 \Imi\Config::get('@server.子服务名.别名1.xxx') 获取
    'configs'    =>    [
        "别名1"    =>    '配置文件路径1',
        "别名2"    =>    '配置文件路径2',
        ……
    ],

    // 如果配置了 configs.别名1，这里的值会被上面的文件覆盖
    '别名1' => [],

    // bean扫描目录，指定命名空间，建议省略
    // 'beanScan'    =>    [
    //     'ImiDemo\WebSocketDemo\Listener',
    // ],
];
```
