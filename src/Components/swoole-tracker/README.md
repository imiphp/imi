# imi-swoole-tracker

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-swoole-tracker.svg)](https://packagist.org/packages/imiphp/imi-swoole-tracker)
[![Php Version](https://img.shields.io/badge/php-%3E=7.4-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.7.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-swoole-tracker.svg)](https://github.com/imiphp/imi-swoole-tracker/blob/master/LICENSE)

## 介绍

在 imi 框架中接入 Swoole Tracker 监控

Swoole Tracker: https://www.swoole-cloud.com/tracker.html

* 企业版集成了Facebook的Xhprof工具，可以生成调用堆栈图和火焰图，亦可生成分层分析表格，方便找到程序瓶颈点进行优化。

* 为解决PHP常驻进程的内存泄漏问题，企业版专门针对PHP的内存泄漏检测工具，方便快速的解决和定位内存持续增长问题。

* Swoole异步/协程模式以及ReactPHP等众多框架最致命的就是阻塞调用，会让并发大大降低，为此我们开发了毫秒级阻塞检测工具，迅速定位导致阻塞的代码。

* 自动抓取FPM、CLI进程数量，统计CPU、内存使用情况。

* 所有工具零部署成本，后台一键开启关闭各种检测，完美支持PHP7。

> 本仓库仅用于浏览，不接受 issue 和 Pull Requests，请前往：<https://github.com/imiphp/imi>

## Swoole Tracker

目前`Swoole Tracker`的`v2.5.0`版本支持自动生成应用名称并创建应用，无需修改任何代码，生成的应用名称格式为：

`Swoole`的`HttpServer`：`ip:prot`

其他的`Server`：`ip(hostname):prot`

即安装好`swoole_tracker`扩展之后就可以正常使用`Swoole Tracker`的功能

## 组件基本使用

1. 在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-swoole-tracker": "~2.0.0"
    }
}
```

2. 执行 `composer update` 安装。

3. 在项目 `config/config.php` 中配置：

```php
[
    'components'    =>  [
        // 引入本组件
        'SwooleTracker'       =>  'Imi\SwooleTracker',
    ],
]
```

### Http 服务

在服务器的 `config/config.php` 中配置：

```php
[
    'beans'    =>    [
        'HttpDispatcher'    =>    [
            'middlewares'    =>    [
                …… // 你的其他中间件
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
                "SwooleTrackerHttpMiddleware", // 放在 RouteMiddlware 后
            ],
        ],
        'SwooleTrackerHttpMiddleware'   =>  [
            'serviceName'   => 'imi-http-example', // 服务名
            // 'serverIp'      => null, // 服务器 IP，默认获取当前网卡 IP
            // 'interface'     => null, // 网卡 interface 名，自动获取当前网卡IP时有效
            // 'successStatusCode' => 200, // 成功的 Http 状态码
        ],
    ],
];
```

### WebSocket 服务

在服务器的 `config/config.php` 中配置：

```php
[
    'beans'    =>    [
        'WebSocketDispatcher'    =>    [
            'middlewares'    =>    [
                …… // 你的其他中间件
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                "SwooleTrackerWebSocketMiddleware", // 放在 RouteMiddlware 后
            ],
        ],
        'SwooleTrackerWebSocketMiddleware'  =>  [
            'serviceName'       => 'imi-websocket-example', // 服务名
            // 'serverIp'          => null, // 服务器 IP，默认获取当前网卡 IP
            // 'interface'         => null, // 网卡 interface 名，自动获取当前网卡IP时有效
            // 'successCode'       =>  500, // 当成功时上报的默认code
            // 'exceptionCode'     =>  500, // 当发生异常时上报的默认code
            // 指定获取请求方法名的参数，必须有
            'nameHandler'       =>  function(\Imi\Server\WebSocket\Message\IFrame $frame){
                return $frame->getFormatData()->action ?? 'unknown'; // 代码仅供参考
            },
        ],
    ],
];
```

### TCP 服务

在服务器的 `config/config.php` 中配置：

```php
[
    'beans'    =>    [
        'TcpDispatcher'    =>    [
            'middlewares'    =>    [
                …… // 你的其他中间件
                \Imi\Server\TcpServer\Middleware\RouteMiddleware::class,
                "SwooleTrackerTCPMiddleware", // 放在 RouteMiddlware 后
            ],
        ],
        'SwooleTrackerTCPMiddleware'  =>  [
            'serviceName'       => 'imi-tcp-example', // 服务名
            // 'serverIp'          => null, // 服务器 IP，默认获取当前网卡 IP
            // 'interface'         => null, // 网卡 interface 名，自动获取当前网卡IP时有效
            // 'successCode'       =>  500, // 当成功时上报的默认code
            // 'exceptionCode'     =>  500, // 当发生异常时上报的默认code
            // 指定获取请求方法名的参数，必须有
            'nameHandler'       =>  function(\Imi\Server\TcpServer\Message\IReceiveData $data){
                return $data->getFormatData()->action ?? 'unknown'; // 代码仅供参考
            },
        ],
    ],
];
```

### UDP 服务

在服务器的 `config/config.php` 中配置：

```php
[
    'beans'    =>    [
        'UdpDispatcher'    =>    [
            'middlewares'    =>    [
                …… // 你的其他中间件
                \Imi\Server\UdpServer\Middleware\RouteMiddleware::class,
                "SwooleTrackerUDPMiddleware", // 放在 RouteMiddlware 后
            ],
        ],
        'SwooleTrackerUDPMiddleware'  =>  [
            'serviceName'       => 'imi-udp-example', // 服务名
            // 'serverIp'          => null, // 服务器 IP，默认获取当前网卡 IP
            // 'interface'         => null, // 网卡 interface 名，自动获取当前网卡IP时有效
            // 'successCode'       =>  500, // 当成功时上报的默认code
            // 'exceptionCode'     =>  500, // 当发生异常时上报的默认code
            'nameHandler'       =>  function(\Imi\Server\UdpServer\Message\IPacketData $data){
                return $data->getFormatData()->action ?? 'unknown'; // 代码仅供参考
            },
        ],
    ],
];
```

## 进阶使用

如果请求产生异常，自动上报失败，错误码为异常 `code`。

你也可以在代码中指定是否成功和错误码，例子：

```php
RequestContext::set('imi.tracker.success', false);
RequestContext::set('imi.tracker.code', 19260817);
```

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.4
- [Composer](https://getcomposer.org/) >= 2.0
- [Swoole](https://www.swoole.com/) >= 4.7.0

## 版权信息

`imi-swoole-tracker` 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://cdn.jsdelivr.net/gh/imiphp/imi@2.0/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
