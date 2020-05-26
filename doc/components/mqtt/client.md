# MQTT 客户端

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

## 使用


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

**will 遗嘱消息参数表：**

| 参数名 | 说明 |
|-|-
| topic | 主题 |
| payload | 有效载荷 |
| qosLevel | 0-最多一次的传输；1-至少一次的传输；2-只有一次的传输 |
| retain | 保留 |
| duplicate | 重复 |
