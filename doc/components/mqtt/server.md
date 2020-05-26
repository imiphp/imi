# MQTT 服务端

MQTT是一个基于客户端-服务器的消息发布/订阅传输协议。MQTT协议是轻量、简单、开放和易于实现的，这些特点使它适用范围非常广泛。在很多情况下，包括受限的环境中，如：机器与机器（M2M）通信和物联网（IoT）。其在，通过卫星链路通信传感器、偶尔拨号的医疗设备、智能家居、及一些小型化设备中已广泛使用。

安装：`composer require imiphp/imi-mqtt`

项目配置文件：

```php
[
    'components'    =>  [
        'MQTT'  =>  'Imi\MQTT',
    ],
]
```

> MQTT 功能要求 PHP >= 7.2

## 配置

首先，服务器配置的`type`设为`MQTT`，并且定义好控制器。

控制器需要继承`Imi\Server\MQTT\BaseMQTTController`类，并且实现方法。

如果你是主服务器，配置如下：

```php
'mainServer'    =>    [
    'namespace'     =>    'ImiApp\MQTTServer',
    'type'          =>    'MQTT',
    'host'          =>    '127.0.0.1',
    'port'          =>    8081,
    'controller'    =>  \ImiApp\MQTTServer\Controller\MQTTController::class,
],
```

如果你是子服务器，配置如下下：

```php
// 子服务器（端口监听）配置
'subServers'        =>    [
    'xxxServer'    =>    [
        'namespace'     =>    'ImiApp\MQTTServer',
        'type'          =>    'MQTT',
        'host'          =>    '127.0.0.1',
        'port'          =>    8081,
        'controller'    =>  \ImiApp\MQTTServer\Controller\MQTTController::class,
    ],
],
```

### 通讯数据包类

`imi-mqtt` 基于 `binsoul/net-mqtt` 开发，使用的都是这个包中的数据包结构类。

类名一般是`BinSoul\Net\Mqtt\Packet\XXX`

如：`\BinSoul\Net\Mqtt\Packet\SubscribeRequestPacket`

## 控制器

```php
<?php
namespace ImiApp\MQTTServer\Controller;

use Imi\Server\MQTT\BaseMQTTController;
use Imi\Server\MQTT\Message\ReceiveData;
use BinSoul\Net\Mqtt\Packet\PublishAckPacket;
use BinSoul\Net\Mqtt\Packet\PingRequestPacket;
use BinSoul\Net\Mqtt\Packet\PingResponsePacket;
use BinSoul\Net\Mqtt\Packet\ConnectRequestPacket;
use BinSoul\Net\Mqtt\Packet\PublishReleasePacket;
use BinSoul\Net\Mqtt\Packet\PublishRequestPacket;
use BinSoul\Net\Mqtt\Packet\ConnectResponsePacket;
use BinSoul\Net\Mqtt\Packet\PublishCompletePacket;
use BinSoul\Net\Mqtt\Packet\PublishReceivedPacket;
use BinSoul\Net\Mqtt\Packet\SubscribeRequestPacket;
use BinSoul\Net\Mqtt\Packet\DisconnectRequestPacket;
use BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket;
use BinSoul\Net\Mqtt\Packet\UnsubscribeRequestPacket;
use BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket;

class MQTTController extends BaseMQTTController
{
    /**
     * 连接
     *
     * @param \BinSoul\Net\Mqtt\Packet\ConnectRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData $receiveData
     * @return \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket|null
     */
    public function connect(ConnectRequestPacket $request, ReceiveData $receiveData): ?ConnectResponsePacket
    {
        $success = 'root' === $request->getUsername() && '123456' === $request->getPassword();
        $response = new ConnectResponsePacket;
        if($success)
        {
            $response->setReturnCode(0);
        }
        else
        {
            $response->setReturnCode(4);
        }
        return $response;
    }

    /**
     * 断开连接
     *
     * @param \BinSoul\Net\Mqtt\Packet\DisconnectRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData $receiveData
     * @return void
     */
    public function disconnect(DisconnectRequestPacket $request, ReceiveData $receiveData): void
    {
        
    }

    /**
     * Ping
     *
     * @param \BinSoul\Net\Mqtt\Packet\PingRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData $receiveData
     * @return \BinSoul\Net\Mqtt\Packet\PingResponsePacket|null
     */
    public function ping(PingRequestPacket $request, ReceiveData $receiveData): ?PingResponsePacket
    {
        return new PingResponsePacket;
    }

    /**
     * 发布
     *
     * @param \BinSoul\Net\Mqtt\Packet\PublishRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData $receiveData
     * @return \BinSoul\Net\Mqtt\Packet\PublishAckPacket|\BinSoul\Net\Mqtt\Packet\PublishReceivedPacket|\BinSoul\Net\Mqtt\Packet\PublishReleasePacket|\BinSoul\Net\Mqtt\Packet\PublishCompletePacket|null
     */
    public function publish(PublishRequestPacket $request, ReceiveData $receiveData)
    {
        switch($request->getTopic())
        {
            case 'a':
                $response = new PublishAckPacket;
                break;
            case 'b':
                $response = new PublishReceivedPacket;
                break;
            case 'c':
                $response = new PublishReleasePacket;
                break;
            case 'd':
                $response = new PublishCompletePacket;
                break;
        }
        $response->setIdentifier($request->getIdentifier());
        return $response;
    }

    /**
     * 订阅
     *
     * @param \BinSoul\Net\Mqtt\Packet\SubscribeRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData $receiveData
     * @return \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket|null
     */
    public function subscribe(SubscribeRequestPacket $request, ReceiveData $receiveData): ?SubscribeResponsePacket
    {
        $response = new SubscribeResponsePacket;
        $response->setIdentifier($request->getIdentifier());
        $response->setReturnCodes([0]);
        return $response;
    }

    /**
     * 取消订阅
     *
     * @param \BinSoul\Net\Mqtt\Packet\UnsubscribeRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData $receiveData
     * @return \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket|null
     */
    public function unsubscribe(UnsubscribeRequestPacket $request, ReceiveData $receiveData): ?UnsubscribeResponsePacket
    {
        $response = new UnsubscribeResponsePacket;
        $response->setIdentifier($request->getIdentifier());
        return $response;
    }

}

```
