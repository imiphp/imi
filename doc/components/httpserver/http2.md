# Http2

imi v1.0.20 新增支持开发 Http2 服务。

具体开发方法和 Http、WebSocket 类似。

需要除了需要加配置外，请求响应对象是使用 Http 的对象。

并且可以像开发 WebSocket 一样使用请求上下文存储数据，因为 Http2 是以全双工长连接的方式工作的。

> 仅支持 Swoole

## 配置方法

在项目 `config.php` 中的服务器配置中加入：

```php
'configs'   =>  [
    'open_http2_protocol'   =>  true,
    // 下面是启用 https，如果需要就配置
    // 'ssl_cert_file'     =>  '/server.crt',
    // 'ssl_key_file'      =>  '/server.key',
],
```

主服务器：

```php
// 主服务器配置
'mainServer'    =>	[
    // 指定服务器命名空间
    'namespace'	=>  'ImiDemo\HttpDemo\MainServer',
    // 服务器类型(http/WebSocket/TcpServer/UdpServer)
    'type'      =>  \Imi\Swoole\Server\Type::HTTP,
    // 监听的IP地址，可选
    'host'      =>  '0.0.0.0',
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
        'open_http2_protocol'   =>  true, // 启用 http2
    ],
],
```

子服务器：

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
            'open_http2_protocol'   =>  true, // 启用 http2
        ],
    ],
]
```

其它用法参考 Http、WebSocket 即可。
