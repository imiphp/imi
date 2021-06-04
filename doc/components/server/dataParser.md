# 数据处理器

数据处理器是用于长连接服务中的，它可以把从客户端接收来的数据，转成 PHP 数组或对象的类。同理，也可以从 PHP 数组或对象转换为数据。

## 配置处理器

**Swoole:**

```php
[
    // 主服务器配置
    'mainServer'    => [
        'namespace'    => 'Imi\Swoole\Test\TCPServer\MainServer',
        'type'         => Imi\Swoole\Server\Type::TCP_SERVER,
        'host'         => imiGetEnv('SERVER_HOST', '127.0.0.1'),
        'port'         => 13003,
        'configs'      => [
            'worker_num'    => 1,
            // EOF自动分包
            'open_eof_split'     => true, //打开EOF_SPLIT检测
            'package_eof'        => "\r\n", //设置EOF
        ],
        // 这里配置
        'dataParser'    => \Imi\Server\DataParser\JsonArrayParser::class,
    ],
    // 子服务器（端口监听）配置
    'subServers'    =>    [
        // 指定服务器命名空间
        'namespace'	=>	'ImiDemo\HttpDemo\MainServer',
        // 服务器类型(http/WebSocket/TcpServer/UdpServer)
        'type'		=>	\Imi\Swoole\Server\Type::TCP_SERVER,
        // 监听的IP地址，可选
        'host'		=>	'0.0.0.0',
        // 监听的端口
        'port'		=>	8080,
        // 参考 swoole sockType，可选
        'sockType'	=>	SWOOLE_SOCK_TCP,
        // 服务器配置，参数用法同\Swoole\Server->set($configs)
        'configs'	=>	[
        ],
        // 这里配置
        'dataParser'    => \Imi\Server\DataParser\JsonArrayParser::class,
    ],
]
```

**Workerman:**

```php

return [
    // Workerman 服务器配置
    'workermanServer' => [
        // 服务器名
        'tcp' => [
            'namespace'   => 'Imi\Workerman\Test\AppServer\TcpServer',
            'type'        => Imi\Workerman\Server\Type::TCP,
            'host'        => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'        => 13003,
            'shareWorker' => 'http',
            'configs'     => [
                'protocol' => \Workerman\Protocols\Text::class,
            ],
            // 这里配置
            'dataParser'    => \Imi\Server\DataParser\JsonArrayParser::class,
        ],
    ],
];
```

## 内置处理器类

### JsonArrayParser

JSON 数组

类名：`\Imi\Server\DataParser\JsonArrayParser::class`

支持注入的属性：

名称 | 描述 | 默认值
-|-|-
options | JSON 序列化时的参数 | 0 |
depth | 设置最大深度。 必须大于0。 | 512 |

### JsonObjectParser

JSON 对象

类名：`\Imi\Server\DataParser\JsonObjectParser::class`

支持注入的属性：

名称 | 描述 | 默认值
-|-|-
options | JSON 序列化时的参数 | 0 |
depth | 设置最大深度。 必须大于0。 | 512 |

## 自定义数据处理器

实现接口：`Imi\Server\DataParser\IParser`

```php
<?php

namespace App\DataParser;

use Imi\Server\DataParser\IParser;

class XXXParser implements IParser
{
    /**
     * 编码为存储格式.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function encode($data)
    {
    }

    /**
     * 解码为php变量.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function decode($data)
    {
    }
}
```
