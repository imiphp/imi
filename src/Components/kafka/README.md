# imi-kafka

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-kafka.svg)](https://packagist.org/packages/imiphp/imi-kafka)
[![Php Version](https://img.shields.io/badge/php-%3E=7.4-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.7.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-kafka.svg)](https://github.com/imiphp/imi-kafka/blob/master/LICENSE)

## 介绍

支持在 imi 框架中使用 Kafka 客户端

支持消息发布和消费

本组件基于 [龙之言](https://longlang.org/) 组织的 [longlang/phpkafka](https://github.com/longyan/phpkafka) 组件，该组件由宇润主导开发。

> 本仓库仅用于浏览，不接受 issue 和 Pull Requests，请前往：<https://github.com/imiphp/imi>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-kafka": "~2.0.0"
    }
}
```

然后执行 `composer update` 安装。

## 使用说明

可以参考 `example` 目录示例，包括完整的消息发布和消费功能。

在项目 `config/config.php` 中配置：

```php
[
    'components'    =>  [
        // 引入组件
        'Kafka'   =>  'Imi\Kafka',
    ],
]
```

连接池配置：

```php
[
    'pools'    =>    [
        'kafka'    => [
            'sync'    => [
                'pool'    => [
                    'class'        => \Imi\Kafka\Pool\KafkaSyncPool::class,
                    'config'       => [
                        'maxResources'    => 10,
                        'minResources'    => 0,
                    ],
                ],
                'resource'    => [
                    'bootstrapServers' => KAFKA_BOOTSTRAP_SERVERS,
                    'groupId'          => 'test',
                    // 其它配置请参考：https://github.com/longyan/phpkafka/blob/master/doc/consumer.md#%E9%85%8D%E7%BD%AE%E5%8F%82%E6%95%B0
                ],
            ],
            'async'    => [
                'pool'    => [
                    'class'        => \Imi\Kafka\Pool\KafkaCoroutinePool::class,
                    'config'       => [
                        'maxResources'    => 10,
                        'minResources'    => 1,
                    ],
                ],
                'resource'    => [
                    'bootstrapServers' => KAFKA_BOOTSTRAP_SERVERS,
                    'groupId'          => 'test',
                ],
            ],
        ],
    ]
]
```

默认连接池：

```php
[
    'beans' =>  [
        'Kafka'  =>  [
            'defaultPoolName'   =>  'kafka',
        ],
    ],
]
```

### 生产者

```php
use Imi\Kafka\Pool\KafkaPool;
use longlang\phpkafka\Producer\ProduceMessage;

// 获取生产者对象
$producer = KafkaPool::getInstance();

// 发送
$producer->send('主题 Topic', '消息内容');
// send 方法定义
// public function send(string $topic, ?string $value, ?string $key = null, array $headers = [], ?int $partitionIndex = null, ?int $brokerId = null): void

// 批量发送
$producer->sendBatch([
    new ProduceMessage($topic, 'v1', 'k1'),
    new ProduceMessage($topic, 'v2', 'k2'),
]);
// sendBatch 方法定义
// public function sendBatch(ProduceMessage[] $messages, ?int $brokerId = null): void
```

### 消费者

**消费者类：**

```php
<?php

namespace ImiApp\Kafka\Test;

use Imi\Bean\Annotation\Bean;
use Imi\Kafka\Annotation\Consumer;
use Imi\Kafka\Base\BaseConsumer;
use Imi\Redis\Redis;
use longlang\phpkafka\Consumer\ConsumeMessage;

/**
 * @Bean("TestConsumer")
 * @Consumer(topic="queue-imi-1", groupId="test-consumer")
 */
class TestConsumer extends BaseConsumer
{
    /**
     * 消费任务
     *
     * @return mixed
     */
    protected function consume(ConsumeMessage $message)
    {
        $messageValue = $message->getValue();
    }
}
```

**消费进程：**

```php
<?php

namespace ImiApp\Process;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Kafka\Contract\IConsumer;
use Imi\Process\Annotation\Process;
use Imi\Process\BaseProcess;

/**
 * @Process(name="TestProcess")
 */
class TestProcess extends BaseProcess
{
    /**
     * @Inject("TestConsumer")
     *
     * @var \ImiApp\Kafka\Test\TestConsumer
     */
    protected $testConsumer;

    public function run(\Swoole\Process $process)
    {
        $this->runConsumer($this->testConsumer);
        \Swoole\Coroutine::yield();
    }

    private function runConsumer(IConsumer $consumer): void
    {
        go(function () use ($consumer) {
            try
            {
                $consumer->run();
            }
            catch (\Throwable $th)
            {
                /** @var \Imi\Log\ErrorLog $errorLog */
                $errorLog = App::getBean('ErrorLog');
                $errorLog->onException($th);
                sleep(3);
                $this->runConsumer($consumer);
            }
        });
    }
}
```

### 注解说明

### @Consumer

消费者注解

| 属性名称 | 说明 |
|-|-
| topic | 主题名称，支持字符串或字符串数组 |
| groupId | 分组ID |
| poolName | 连接池名称，不传则使用配置中默认的 |

### 队列组件支持

本组件额外实现了 [imiphp/imi-queue](https://github.com/imiphp/imi-queue) 的接口，可以用 Queue 组件的 API 进行调用。

只需要将队列驱动配置为：`KafkaQueueDriver`

配置示例：

```php
[
    'components'    =>  [
        'Kafka'  =>  'Imi\Kafka',
    ],
    'beans' =>  [
        'AutoRunProcessManager' =>  [
            'processes' =>  [
                // 加入队列消费进程，非必须，你也可以自己写进程消费
                'QueueConsumer',
            ],
        ],
        'imiQueue'  => [
            // 默认队列
            'default'   => 'QueueTest1',
            // 队列列表
            'list'  => [
                // 队列名称
                'QueueTest1' => [
                    // 使用的队列驱动
                    'driver'        => 'KafkaQueueDriver',
                    // 消费协程数量
                    'co'            => 1,
                    // 消费进程数量；可能会受进程分组影响，以同一组中配置的最多进程数量为准
                    'process'       => 1,
                    // 消费循环尝试 pop 的时间间隔，单位：秒（仅使用消费者类时有效）
                    'timespan'      => 0.1,
                    // 进程分组名称
                    'processGroup'  => 'a',
                    // 自动消费
                    'autoConsumer'  => true,
                    // 消费者类
                    'consumer'      => 'QueueTestConsumer',
                    // 驱动类所需要的参数数组
                    'config'        => [
                        // Kafka 连接池名称
                        'poolName' => 'kafka',
                        // 分组ID，如果不传或为null则使用连接池中的配置
                        'groupId'  => 'g1',
                    ],
                ],
            ],
        ],
    ]
]
```

消费者类写法，与`imi-queue`组件用法一致。

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.4
- [Composer](https://getcomposer.org/) >= 2.0
- [Swoole](https://www.swoole.com/) >= 4.7.0

## 版权信息

`imi-kafka` 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://cdn.jsdelivr.net/gh/imiphp/imi@2.0/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
