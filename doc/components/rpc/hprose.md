# Hprose

## 介绍

在 imi 框架中集成 Hprose 服务开发，目前支持`TCP`、`Unix Socket`协议。

Github: <https://github.com/imiphp/imi-hprose>

> 仅支持 Swoole

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-hprose": "2.0.x-dev"
    }
}
```

然后执行 `composer update` 安装。

## 服务端

在项目 `config/config.php` 中配置：

```php
[
    'components'    =>  [
        // 引入RPC组件以及本组件
        'Rpc'        =>  'Imi\Rpc',
        'Hprose'    =>  'Imi\Hprose',
    ],
]
```

如果你用主服务器：

```php
[
    // 主服务器配置
    'mainServer'    =>    [
        'namespace'    =>    'XXX\MainServer', // 你的命名空间
        'type'        =>    'Hprose', // 必须设为 Hprose
        'port'        =>    8080,
    ],
]
```

如果你用子服务器：

```php
[
    // 子服务器（端口监听）配置
    'subServers'        =>    [
        // 子服务器名
        'XXX'    =>    [
            'namespace'    =>    'XXX\Hprose', // 你的命名空间
            'type'        =>    'Hprose', // 必须设为 Hprose
            'port'        =>    50001,
        ]
    ],
]
```

### 控制器基类

`Imi\Rpc\Controller\RpcController`

### 控制器注解

`\Imi\Rpc\Route\Annotation\RpcController`

用法：

`@RpcController`

别名前缀：`@RpcController("a_b_")`

指定服务器：`@RpcController(prefix="a_b_", server="服务器名")`

### 动作注解

`\Imi\Rpc\Route\Annotation\RpcAction`

无参

### 路由注解

`\Imi\Hprose\Route\Annotation\HproseRoute`

参数：

- `name` 路由名称规则。一般也是方法名，如果设置了别名，则最终为别名+方法名
- `mode` 该设置表示该服务函数返回的结果类型，具体值请参考 Hprose 文档
- `simple` 该设置表示本服务函数所返回的结果是否为简单数据。默认值为 false。
- `oneway` 该设置表示本服务函数是否不需要等待返回值。当该设置为 true 时，调用会异步开始，并且不等待结果，立即返回 null 给客户端。默认值为 false。
- `async` 该设置表示本服务函数是否为异步函数，异步函数的最后一个参数是一个回调函数，用户需要在异步函数中调用该回调方法来传回返回值
- `passContext` 该属性为 boolean 类型，默认值为 false。具体请参考 Hprose 文档

> Hprose 文档参考：<https://github.com/hprose/hprose-php/wiki/06-Hprose-服务器#addfunction-%E6%96%B9%E6%B3%95>

## 客户端

### 连接池配置

```php
[
    'pools'    =>    [
        '连接池名'    =>    [
            'sync'    =>    [
                'pool'    =>    [
                    'class'        =>    \Imi\Rpc\Client\Pool\RpcClientSyncPool::class,
                    'config'    =>    [
                        // 连接池通用，查阅文档
                    ],
                ],
                'resource'    =>    [
                    'clientClass'    =>    \Imi\Hprose\Client\HproseSocketClient::class,
                    'uris'    =>    'tcp://127.0.0.1:50001', // 连接地址
                    // 其它配置
                ]
            ],
            'async'    =>    [
                'pool'    =>    [
                    'class'        =>    \Imi\Rpc\Client\Pool\RpcClientCoroutinePool::class,
                    'config'    =>    [
                        // 连接池通用，查阅文档
                    ],
                ],
                'resource'    =>    [
                    'clientClass'    =>    \Imi\Hprose\Client\HproseSocketClient::class,
                    'uris'    =>    'tcp://127.0.0.1:50001', // 连接地址
                    // 其它配置
                ]
            ],
        ],
    ],
    'rpc'    =>    [
        'defaultPool'    =>    '连接池名', // 默认连接池名
    ],
]
```

### 客户端调用

代码调用：

```php
\Imi\Rpc\Client\Pool\RpcClientPool::getService('服务名')->方法名(参数);
```

注解调用：

```php
class Test
{
    /**
     * @RpcClient()
     *
     * @var \Imi\Rpc\Client\IRpcClient
     */
    protected $rpcClient;

    /**
     * @RpcService(serviceName="服务名")
     *
     * @var \Imi\Rpc\Client\IService
     */
    protected $xxxRpc;

    public function aaa()
    {
        // 方法一
        $this->rpcClient->getService('服务名')->方法名(参数);

        // 方法二
        $this->xxxRpc->方法名(参数);
    }
}
```
