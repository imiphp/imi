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
