# imi-mqtt

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-mqtt.svg)](https://packagist.org/packages/imiphp/imi-mqtt)
[![Php Version](https://img.shields.io/badge/php-%3E=7.2-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.4.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-mqtt.svg)](https://github.com/imiphp/imi-mqtt/blob/master/LICENSE)

## 介绍

在 imi 框架中开发 MQTT 服务端，并且内置了一个 MQTT 客户端。

MQTT是一个基于客户端-服务器的消息发布/订阅传输协议。MQTT协议是轻量、简单、开放和易于实现的，这些特点使它适用范围非常广泛。在很多情况下，包括受限的环境中，如：机器与机器（M2M）通信和物联网（IoT）。其在，通过卫星链路通信传感器、偶尔拨号的医疗设备、智能家居、及一些小型化设备中已广泛使用。

> 本仓库仅用于浏览，不接受 issue 和 Pull Requests，请前往：<https://github.com/imiphp/imi>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-mqtt": "^1.0.0"
    }
}
```

然后执行 `composer update` 安装。

## 使用说明

> 可以参考 `example`、`tests` 目录示例。

项目配置文件：

```php
[
    'components'    =>  [
        'MQTT'  =>  'Imi\MQTT',
    ],
]
```

### MQTT 通讯数据包类

`imi-mqtt` 基于 `binsoul/net-mqtt` 开发，使用的都是这个包中的数据包结构类。

类名一般是`BinSoul\Net\Mqtt\Packet\XXX`

如：`\BinSoul\Net\Mqtt\Packet\SubscribeRequestPacket`

### MQTT 服务开发

首先，服务器配置的`type`设为`MQTT`，并且定义好控制器。

控制器需要继承`Imi\Server\MQTT\BaseMQTTController`类，并且实现方法。

如果你是主服务器，配置如下：

```php
'mainServer'    =>    [
    'namespace'     =>    'ImiApp\MQTTServer',
    'type'          =>    'MQTTServer',
    'host'          =>    '127.0.0.1',
    'port'          =>    8081,
    'controller'    =>  \ImiApp\MQTTServer\Controller\MQTTController::class,
    // 'configs'       =>    [
    //     // 启用 MQTTS 配置证书
    //     'ssl_cert_file'     =>  dirname(__DIR__) . '/ssl/server.crt',
    //     'ssl_key_file'      =>  dirname(__DIR__) . '/ssl/server.key',
    // ],
],
```

如果你是子服务器，配置如下下：

```php
// 子服务器（端口监听）配置
'subServers'        =>    [
    'xxxServer'    =>    [
        'namespace'     =>    'ImiApp\MQTTServer',
        'type'          =>    'MQTTServer',
        'host'          =>    '127.0.0.1',
        'port'          =>    8081,
        'controller'    =>  \ImiApp\MQTTServer\Controller\MQTTController::class,
        // 'configs'       =>    [
        //     // 启用 MQTTS 配置证书
        //     'ssl_cert_file'     =>  dirname(__DIR__) . '/ssl/server.crt',
        //     'ssl_key_file'      =>  dirname(__DIR__) . '/ssl/server.key',
        // ],
    ],
],
```

在控制器方法中返回一个包对象，代表响应当前请求。

同样支持`\Imi\Server\Server::send()`等方法，详见：<https://doc.imiphp.com/utils/Server.html>

### MQTT 客户端开发

**事件监听类：**

```php
<?php
namespace Imi\MQTT\Test;

use Imi\MQTT\Client\Contract\IMQTTClientListener;

class TestClientListener implements IMQTTClientListener
{
    /**
     * 连接确认
     *
     * @param \Imi\MQTT\Client\MQTTClient $client
     * @param \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket $packet
     * @return void
     */
    public function connectACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket $packet)
    {
    }

    /**
     * 发布
     *
     * @param \Imi\MQTT\Client\MQTTClient $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishRequestPacket $packet
     * @return void
     */
    public function publish(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishRequestPacket $packet)
    {
    }

    /**
     * 发布确认
     *
     * @param \Imi\MQTT\Client\MQTTClient $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishAckPacket $packet
     * @return void
     */
    public function publishAck(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishAckPacket $packet)
    {
    }

    /**
     * 发布已收到（保证交付部分1）
     *
     * @param \Imi\MQTT\Client\MQTTClient $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishReceivedPacket $packet
     * @return void
     */
    public function publishReceived(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishReceivedPacket $packet)
    {
    }

    /**
     * 发布释放（确保交付的第2部分）
     *
     * @param \Imi\MQTT\Client\MQTTClient $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishReleasePacket $packet
     * @return void
     */
    public function publishRelease(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishReleasePacket $packet)
    {
    }

    /**
     * 发布完成（保证交付的第3部分）
     *
     * @param \Imi\MQTT\Client\MQTTClient $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishCompletePacket $packet
     * @return void
     */
    public function publishComplete(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishCompletePacket $packet)
    {
    }

    /**
     * 订阅确认
     *
     * @param \Imi\MQTT\Client\MQTTClient $client
     * @param \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket $packet
     * @return void
     */
    public function subscribeACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket $packet)
    {
    }

    /**
     * 取消订阅确认
     *
     * @param \Imi\MQTT\Client\MQTTClient $client
     * @param \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket $packet
     * @return void
     */
    public function unsubscribeACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket $packet)
    {
    }

    /**
     * Ping 响应
     *
     * @param \Imi\MQTT\Client\MQTTClient $client
     * @param \BinSoul\Net\Mqtt\Packet\PingResponsePacket $packet
     * @return void
     */
    public function ping(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PingResponsePacket $packet)
    {
    }
}
```

**客户端调用：**

```php
use Imi\MQTT\Client\MQTTClient;
$client = new MQTTClient([
    'host'          =>  '127.0.0.1',
    'port'          =>  8081,
], new TestClientListener);
$client->wait(); // 开始循环接收，直到关闭连接
```

**客户端参数表：**

| 参数名 | 说明 |
|-|-
| host | 服务器主机名称 |
| port | 服务器端口号 |
| timeout | 网络通讯超时时间 |
| pingTimespan | 定时 ping 的时间间隔，单位秒。默认为 `NULL` 则不自动 ping |
| protocol | 协议级别，默认`4`。`3-MQIsdp;4-MQTT` |
| username | 用户名 |
| password | 密码 |
| clientId | 客户端ID |
| keepAlive | 保活时间 |
| clean | 清除会话 |
| will | 遗嘱消息，具体结构看下面的表格 |
| ssl | 使用 SSL 连接 |
| sslCertFile | 证书文件 |
| sslKeyFile | 证书密钥文件 |
| sslVerifyPeer | 验证服务器端证书 |
| sslAllowSelfSigned | 允许自签名证书 |
| sslHostName | 服务器主机名称 |
| sslCafile | CA 证书 |
| sslCapath | 证书目录 |

**will 遗嘱消息参数表：**

| 参数名 | 说明 |
|-|-
| topic | 主题 |
| payload | 有效载荷 |
| qosLevel | 0-最多一次的传输；1-至少一次的传输；2-只有一次的传输 |
| retain | 保留 |
| duplicate | 重复 |

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.2
- [Composer](https://getcomposer.org/)
- [Swoole](https://www.swoole.com/) >= 4.4.0

## 版权信息

`imi-mqtt` 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://raw.githubusercontent.com/imiphp/imi-mqtt/dev/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
