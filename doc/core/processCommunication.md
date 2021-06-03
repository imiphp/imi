# 内部进程间通讯

为了方便进程内部通讯，进行数据交换。imi v1.2.0 版本新增了内部进程间通讯封装。

我们只需要使用 `Server::sendMessage()` 发送，使用监听事件接收就行了。

**支持：** Swoole、Workerman

## Swoole

使用 Swoole 提供的 [sendMessage()](http://wiki.swoole.com/#/server/methods?id=sendmessage) 和 [onPipeMessage 事件](http://wiki.swoole.com/#/server/events?id=onpipemessage) 实现。

在 `onPipeMessage` 事件中，收到指定结构的数据，就会触发相应事件。

类名：`Imi\Swoole\Server\Server`

## Workerman

使用 [Channel分布式通讯组件](http://doc3.workerman.net/315195) 实现。

类名：`Imi\Workerman\Server\Server`

## 介绍

### 数据结构

```php
[
    'action'    =>  '动作名', // 此字段固定
    // 其它参数任意增加即可
]
```

### 事件名称

`IMI.PIPE_MESSAGE.动作名`

## 代码示例

### 发送

```php
use Imi\Swoole\Server\Server;

// 发送给所有 Worker 进程
Server::sendMessage('test', [
    'time'  =>  time(),
]);

// 发送给 WorkerId 为 1 的进程
Server::sendMessage('test', [
    'time'  =>  time(),
], 1);
```

### 监听

事件名称为：`IMI.PIPE_MESSAGE.test`

```php
<?php
namespace App\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener("IMI.PIPE_MESSAGE.test")
 */
class TestMessage implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        $data = $e->getData()['data'];
        var_dump($data['time']); // 接收到了上面发送来的 time
    }

}
```

### 发送并获取返回数据

**思路：**

一般来讲，发送消息不像 http 请求，一定会有响应结果。

但有时候，我们需要获取返回数据。

办法很简单，比如：发送数据动作名为 `testRequest`，再定义一个 `testResponse` 动作监听用于接收数据即可。

再使用 `Channel` 挂起协程等待响应结果，完美！

> 暂时只有 Swoole 支持

**发送请求并等待响应：**

```php
use Imi\Swoole\Server\Server;
use Imi\Swoole\Util\Co\ChannelContainer;

// 生成一个随机ID
$id = uniqid('', true);

try {
    $channel = ChannelContainer::getChannel($id);
    // 发送给 WorkerId 为 1 的进程
    Server::sendMessage('testRequest', [
        'time'  =>  time(),
    ], 1);
    // 通过 Channel 获取结果，超时时间可以自行设置，这里是 30 秒
    $result = $channel->pop(30);
    if(false === $result)
    {
        throw new \RuntimeException('Receive error');
    }
    var_dump($result['datetime']); // 返回结果
} finally {
    ChannelContainer::removeChannel($id);
}
```

**监听请求：**

事件名称为：`IMI.PIPE_MESSAGE.testRequest`

```php
<?php
namespace App\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Server\Server;

/**
 * @Listener("IMI.PIPE_MESSAGE.testRequest")
 */
class TestRequestMessage implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        $data = $e->getData()['data'];
        $datetime = date('Y-m-d H:i:s', $data['time']);

        // 发送响应结果
        Server::sendMessage('testResponse', [
            'messageId' =>  $data['messageId'];
            'datetime'  =>  $datetime,
        ], $e->getData()['workerId']);
    }

}
```

**监听响应：**

> 暂时只有 Swoole 支持

事件名称为：`IMI.PIPE_MESSAGE.testResponse`

```php
<?php
namespace App\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Server\Server;
use Imi\Swoole\Util\Co\ChannelContainer;

/**
 * @Listener("IMI.PIPE_MESSAGE.testResponse")
 */
class TestResponseMessage implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        $data = $e->getData()['data'];
        if(ChannelContainer::hasChannel($data['messageId']))
        {
            // 推结果进 Channel
            ChannelContainer::push($data['messageId'], $data);
        }
    }

}
```
