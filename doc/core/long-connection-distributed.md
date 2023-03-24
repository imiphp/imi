# 长连接分布式解决方案

[toc]

imi v2.0 开始，新增了长连接分布式解决方案。

该方案支持在 TCP、WebSocket 长连接服务中，轻松实现原生分布式推送功能。

使用分布式解决方案时，你对连接的绑定、分组、推送等操作，底层都会自动实现逻辑，极大地降低了心智负担，可以说是一把梭！

组件引入：`composer require imiphp/imi-workerman-gateway`

Demo: `composer create-project imiphp/project-websocket:~2.0`

## 模式

### 消息队列模式

#### AMQP

imi v2.0 开始，新增了支持 RabbitMQ 或其他支持 AMQP 协议的消息队列。

使用发布订阅模式，每个消费者（Worker 进程）都是一个绑定到交换机的独立队列。

需要注意的是，无法准确判断指定连接是否存在于其他服务，需要业务层面自行实现。

此外，所有模式都不推荐使用持久化特性，因为没有实际意义。

> 此模式仅支持 Swoole

##### AMQP 一把梭模式

会忽略 `routingKey`，将所有队列都接收所有指令，使用起来简单易用，但是性能相对较差。

**用法：**

项目配置文件：

```php
'imi' => [
    'beans' => [
        'ServerUtil' => 'AmqpServerUtil',
    ],
],
'beans' => [
    'AmqpServerUtil' => [
        // 'amqpName' => null, // amqp 连接名称
        // 交换机配置，同 AMQP 组件的 @Exchange 注解参数
        'exchangeConfig' => [
            'name' => 'imi_server_util_test', // 交换机名
            'type' => \PhpAmqpLib\Exchange\AMQPExchangeType::FANOUT, // fanout 模式
        ],
        // 队列配置，同 AMQP 组件的 @Queue 注解参数
        'queueConfig' => [
            'name'    => 'imi_server_util_', // 每个进程中的队列名前缀，如果是多实例部署，请设为不同的
            'durable' => false, // 非持久化
        ],
        // 'consumerClass' => 'AmqpServerConsumer', // 消费者类，如有需要可以覆盖自己实现
        // 'publisherClass' => 'AmqpServerPublisher', // 发布者类，如有需要可以覆盖自己实现
    ],
],
```

##### AMQP 路由模式

在路由模式下，队列会根据指定的 routing key 接收指定的消息。

使用路由模式需要配置对应的 GroupHandler 和 ConnectionContextHandler。

当绑定、解绑 Group 或者更改 flag 时，对应的交换机、队列、routing key 也会被绑定和解绑。

因此，在消费者接收消息时，只会接收与自己相关的消息。

**用法：**

项目配置文件：

```php
'imi' => [
    'beans' => [
        'ServerUtil' => 'AmqpServerUtil',
    ],
],
'beans' => [
    'ServerGroup' => [
        'groupHandler' => 'GroupAmqp', // 配置对应的 GroupHandler
    ],
    'ConnectionContextStore'   => [
        'handlerClass'  => 'ConnectionContextAmqp', // 配置对应的 ConnectionContextHandler
    ],
    'AmqpServerUtil' => [
        // 'amqpName' => null, // amqp 连接名称
        // 交换机配置，同 AMQP 组件的 @Exchange 注解参数
        'exchangeConfig' => [
            'name' => 'imi_server_util_test', // 交换机名
            'type' => \PhpAmqpLib\Exchange\AMQPExchangeType::DIRECT, // direct 模式
        ],
        // 队列配置，同 AMQP 组件的 @Queue 注解参数
        'queueConfig' => [
            'name'    => 'imi_server_util_', // 每个进程中的队列名前缀，如果是多实例部署，请设为不同的
            'durable' => false, // 非持久化
        ],
        // 'consumerClass' => 'AmqpServerConsumer', // 消费者类，如有需要可以覆盖自己实现
        // 'publisherClass' => 'AmqpServerPublisher', // 发布者类，如有需要可以覆盖自己实现
    ],
],
```

#### Redis

该方案采用 Redis 发布订阅实现，每个运行实例都连接到 Redis 并订阅，当有消息发布时，Redis 会发送给所有订阅的服务器，服务器再进行消息的推送，从而实现了原生分布式推送功能。

这个解决方案是基于 Redis 的发布订阅机制实现的，可以轻松地在多个服务器之间共享消息。因为 Redis 是一种高性能的内存数据库，所以它可以轻松处理大量消息，从而确保在分布式环境下推送消息的效率和可靠性。

> 此模式仅支持 Swoole

**用法：**

项目配置文件：

```php
'imi' => [
    'beans' => [
        'ServerUtil' => 'RedisServerUtil',
    ],
],
'beans' => [
    'RedisServerUtil' => [
        'redisName' => null, // 配置的 Redis 连接名称，为 null 则使用默认
        'channel' => 'imi:RedisServerUtil:channel', // 发布订阅的频道名，不同服务请设为不同的，以防冲突
    ],
],
```

### 网关模式

#### Workerman Gateway

Workerman Gateway 是一个成熟的实现方案，可以实现分布式消息推送，同时也支持不断线更新业务代码。这使得它特别适合处理海量设备的物联网项目。

在使用 Workerman Gateway 的过程中，需要注意编写的代码运行在 Worker 进程中，因此需要单独配置 Worker 类型的服务器配置。这与纯粹的 Swoole、Workerman 服务配置略有不同。

如果您想了解更多关于 Workerman Gateway 的知识，可以参考其官方文档：<http://doc4.workerman.net/>

> 此模式支持 Swoole、Workerman

**Swoole 用法：**

项目配置文件：

```php
[
    // 主服务器配置（子服务器配置同理）
    'mainServer'    => defined('SWOOLE_VERSION') ? [
        'namespace'    => '服务命名空间',
        'type'         => \Imi\WorkermanGateway\Swoole\Server\Type::BUSINESS_WEBSOCKET, // WebSocket 业务服务器
        // 'type'         => \Imi\WorkermanGateway\Swoole\Server\Type::BUSINESS_TCP, // TCP 业务服务器
        'mode'         => \SWOOLE_BASE,
        //网关配置
        'workermanGateway' => [
            // worker 名称，在不同的 worker 实例中必须不同，一般推荐环境变量来修改
            'workerName'           => 'websocketWorker',
            'registerAddress'      => '127.0.0.1:13004', // 注册中心地址
            'worker_coroutine_num' => swoole_cpu_num(), // 每个 Worker 进程中的工作协程数量
            // 待处理任务通道长度
            'channel'              => [
                'size' => 1024,
            ],
        ],
    ] : [],
    'swoole' => [
        'imi' => [
            'beans' => [
                'ServerUtil' => Imi\WorkermanGateway\Swoole\Server\Util\GatewayServerUtil::class,
            ],
        ],
    ],
]
```

配置好后用 `vendor/bin/imi-swoole swoole/start` 启动服务即可。

**Workerman 用法：**

项目配置文件：

```php
[
    // Workerman 服务器配置
    'workermanServer' => [
        // Worker 配置
        // worker 名称 websocket，在不同的 worker 实例中必须不同，一般推荐环境变量来修改
        'websocket' => [
            'namespace'   => '服务命名空间',
            'type'        => Imi\WorkermanGateway\Workerman\Server\Type::BUSINESS_WEBSOCKET, // WebSocket 业务服务器
            // 'type'        => Imi\WorkermanGateway\Workerman\Server\Type::BUSINESS_TCP, // TCP 业务服务器
            'configs'     => [
                'registerAddress' => '127.0.0.1:13004', // 注册中心地址
                'count'           => 2,
            ],
        ],
        // 其它监听端口的服务，可以不要
        'http' => [
            'namespace' => 'Imi\WorkermanGateway\Test\AppServer\ApiServer',
            'type'      => Imi\Workerman\Server\Type::HTTP,
            'host'      => \Imi\env('SERVER_HOST', '127.0.0.1'),
            'port'      => 13000,
            'configs'   => [
                'registerAddress' => '127.0.0.1:13004',
            ],
        ],
    ],
    'workerman' => [
        'imi' => [
            'beans' => [
                'ServerUtil' => Imi\WorkermanGateway\Workerman\Server\Util\GatewayServerUtil::class,
            ],
        ],
    ],
]
```

按上面的配置好后，如果你的网关和注册中心是单独运行的，那么已经可以跑了。

如果你希望用 imi 来启动网关和注册中心，可以参考如下配置。

在 `@app.workermanServer` 配置中加入：

```php
[
    // 注册中心服务
    'register' => [
        'namespace'   => 'Imi\WorkermanGateway\Test\AppServer\Register',
        'type'        => Imi\WorkermanGateway\Workerman\Server\Type::REGISTER,
        'host'        => '0.0.0.0',
        'port'        => 13004,
        'configs'     => [
        ],
    ],
    // 网关服务
    'gateway' => [
        'namespace'   => 'Imi\WorkermanGateway\Test\AppServer\Gateway',
        'type'        => Imi\WorkermanGateway\Workerman\Server\Type::GATEWAY,
        'socketName'  => 'websocket://0.0.0.0:13002',
        'nonControlFrameType' => \Imi\Server\WebSocket\Enum\NonControlFrameType::TEXT, // 配置 WebSocket 纯文本通信协议
        // 'nonControlFrameType' => \Imi\Server\WebSocket\Enum\NonControlFrameType::BINARY, // 配置 WebSocket 二进制通信协议
        'configs'     => [
            'lanIp'           => '127.0.0.1',
            'startPort'       => 12900,
            'registerAddress' => '127.0.0.1:13004',
        ],
    ],
]
```

配置好后用 `vendor/bin/imi-workerman workerman/start` 启动服务即可。
