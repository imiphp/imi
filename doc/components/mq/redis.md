# Redis 消息队列

## 介绍

[imi 框架](https://github.com/Yurunsoft/imi)的队列组件，使用 Redis 作为持久化

支持的特性：

- 消息持久化
- 分布式
- 延时消息
- ACK 机制
- 消费超时机制
- 失败/超时消息重新消费

项目地址：<https://github.com/imiphp/imi-queue>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-queue": "^1.0.0"
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
        'Queue'  =>  'Imi\Queue',
    ],
    'beans' =>  [
        'AutoRunProcessManager' =>  [
            'processes' =>  [
                // 加入队列消费进程，非必须，你也可以自己写进程消费
                'QueueConsumer',
            ],
        ],
        'imiQueue'  =>  [
            // 默认队列
            'default'   =>  'test1',
            // 队列列表
            'list'  =>  [
                // 队列名称
                'test1' =>  [
                    // 使用的队列驱动
                    'driver'        =>  \Imi\Queue\Driver\RedisQueueDriver::class,
                    // 消费协程数量
                    'co'            =>  1,
                    // 消费进程数量；可能会受进程分组影响，以同一组中配置的最多进程数量为准
                    'process'       =>  1,
                    // 消费循环尝试 pop 的时间间隔，单位：秒
                    'timespan'      =>  0.1,
                    // 进程分组名称
                    'processGroup'  =>  'a',
                    // 自动消费
                    'autoConsumer'  =>  true,
                    // 消费者类
                    'consumer'      =>  'AConsumer',
                    // 驱动类所需要的参数数组
                    'config'        =>  [
                        'poolName'  =>  'redis',
                        'prefix'    =>  'imi:queue:test:',
                    ]
                ],
            ],
        ],
    ]
]
```

### 消费者类

```php
<?php
namespace ImiApp\Consumer;

use Imi\Log\Log;
use Imi\Bean\Annotation\Bean;
use Imi\Queue\Contract\IMessage;
use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Service\BaseQueueConsumer;

/**
 * @Bean("AConsumer")
 */
class AConsumer extends BaseQueueConsumer
{
    /**
     * 处理消费
     * 
     * @param \Imi\Queue\Contract\IMessage $message
     * @param \Imi\Queue\Driver\IQueueDriver $queue
     * @return void
     */
    protected function consume(IMessage $message, IQueueDriver $queue)
    {
        Log::info(sprintf('[%s]%s:%s', $queue->getName(), $message->getMessageId(), $message->getMessage()));
        $queue->success($message);
    }

}
```

### 获取队列对象

```php
use \Imi\Queue\Facade\Queue;
$queue = Queue::getQueue('队列名称');
```

### 推送消息到队列

返回消息ID

```php
$message = new \Imi\Queue\Model\Message;
$message->setMessage('字符串的消息内容');
$message->setWorkingTimeout(0); // 设置工作超时时间，单位：秒，为0不限制
$queue->push($message);
// 延时消息，单位：秒
$queue->push($message, 1.5);
```

### 从队列弹出一个消息

```php
$message = $queue->pop();
if(null !== $message)
{
    // 将消息标记为成功
    $queue->success($message);

    // 将消息标记为失败
    $queue->fail($message);
}
```

### 删除消息

```php
$message = $queue->pop();
if(null !== $message)
{
    $queue->delete($message);
}
```

### 清空队列

```php
use \Imi\Queue\Enum\QueueType;

$queue->clear(); // 清空全部

// 清空指定类型
$queue->clear([
    QueueType::READY,   // 准备就绪
    QueueType::WORKING, // 工作中
    QueueType::FAIL,    // 失败
    QueueType::TIMEOUT, // 超时
    QueueType::DELAY,   // 准备就绪延时
]);
```

### 获取队列状态

```php
// 返回 \Imi\Queue\Model\QueueStatus 类型
$status = $queue->status();
$status->getReady();    // 准备就绪数量
$status->getWorking();  // 工作中数量
$status->getFail();     // 失败数量
$status->getTimeout();  // 超时数量
$status->getDelay();    // 延时数量
```

### 将失败消息恢复到队列

让失败消息可以被重新消费

```php
$queue->restoreFailMessages();
```

### 将超时消息恢复到队列

让超时消息可以被重新消费

```php
$queue->restoreTimeoutMessages();
```

## 命令行工具

### 获取队列状态

命令：`bin/imi queue/status -queue 队列名称`

返回 JSON：

```js
{
    "ready": 0,
    "working": 0,
    "fail": 0,
    "timeout": 0,
    "delay": 0
}
```

### 将失败消息恢复到队列

命令：`bin/imi queue/restoreFail -queue 队列名称`

返回恢复的消息数量：

```js
0
```

### 将超时消息恢复到队列

命令：`bin/imi queue/restoreTimeout -queue 队列名称`

返回恢复的消息数量：

```js
0
```
