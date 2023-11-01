# InfluxDB

[toc]

InfluxDB 是一个开源的时间序列数据库，没有外部依赖性。它对记录指标、事件和执行分析很有用。

项目地址：<https://github.com/influxdata/influxdb>

imi-influxdb：<https://github.com/imiphp/imi-influxdb>

> 目前 imi 仅支持 InfluxDB < 1.8

## 说明

imi 支持将服务指标监控的数据写入 InfluxDB。

## 安装

`composer require imiphp/imi-influxdb:~3.0.0`

## 使用说明

imi-influxdb 基础配置和使用说明详见：[链接](/v3.0/components/influxdb.html)

### 服务指标监控

仅支持 Swoole、Workerman。

支持 TDengine InfluxDB 协议写入。

#### 安装所需组件

`composer require imiphp/imi-meter:~3.0.0`

#### 配置

**配置监控指标：**

`@app.beans`：

```php
[
    'MeterRegistry' => [
        'driver'  => \Imi\InfluxDB\Meter\InfluxDBMeterRegistry::class,
        'options' => [
            'database'   => null, // 使用的数据库名，可以设为null使用连接中配置的数据库名
            'clientName' => null, // 连接客户端名称，可以设为null使用默认客户端名称
            'batch'      => 1000, // 单次推送的记录数量
            'interval'   => 0, // 推送时间周期，单位：秒，默认为0不启用推送，如希望监控生效，请设置一个合理的数值。
            // 所有标签如设为空字符串或 null 则忽略该标签
            'resultTag' => 'result', // 标签名-结果
            'exceptionTag' => 'exception', // 标签名-异常类名
            'instanceTag' => 'instance', // 标签名-实例
            'workerTag' => 'worker', // 标签名-WorkerId
            'instance' => 'imi', // 当前实例名称，每个实例要设置不同
        ],
    ],
]
```

**TDengine InfluxDB 所需修改的配置：**

```php
// 连接配置一定要设置这几项
[
    'port'              => 6041,
    'path'              => '/influxdb/v1/',
    'createDatabase'    => false,
    'username'          => 'root',
    'password'          => 'taosdata',
]
```

#### 使用

##### 内置监控

###### 连接池监控

`@app.beans`：

```php
// 连接池监控
'PoolMonitor' => [
    'enable'         => true, // 启用
    // 'pools'       => null, // 监控的连接池名称数组。如果为 null 则代表监控所有连接池
    // 'interval'    => 10, // 上报时间间隔，单位：秒
    // 'countKey'    => 'pool_count', // 连接总数量键名
    // 'usedKey'     => 'pool_used', // 忙碌连接数量键名
    // 'freeKey'     => 'pool_free', // 空间连接数量键名
    // 'workerIdTag' => 'worker_id', // 工作进程 ID 标签名
    // 'poolNameTag' => 'pool_name', // 连接池标签名
],
```

###### Swoole 服务器指标监控

`@app.beans`：

```php
// Swoole 服务器指标监控
'SwooleServerMonitor' => [
    'enable'         => true, // 启用
    // 要监控的指标名称数组
    // 详见：https://wiki.swoole.com/#/server/methods?id=stats
    // 格式1：stats() 指标名称 => 实际指标名称
    // 格式2：stats() 指标名称同时作为实际指标名称
    'stats'          => [
        'connection_num' => 'swoole_connection',
        'request_count'  => 'swoole_request_count',
        'coroutine_num'  => 'swoole_coroutine_num',
        'coroutine_num',
    ],
    // 'interval'    => 10, // 上报时间间隔，单位：秒
    // 'workerIdTag' => 'worker_id', // 工作进程 ID 标签名
],
```

##### 注解

###### @Counted

类名：`\Imi\Meter\Annotation\Counted`

计数统计，适合只累加，不减少的统计数据类型。

例如：访问次数统计。

| 参数名 | 类型 | 默认值  | 描述 |
| ------ | ------ | ------ | ------ |
| name | `string` | `imi.counted` | 指标名称 |
| recordFailuresOnly | `false` | `bool` | 是否只在抛出异常时记录 |
| tags | `array` | `[]` | 标签，键值数组 |
| description | `string` |  | 描述 |
| options | `array` | `[]` | 额外参数，每个驱动不同 |

###### @Gauged

类名：`\Imi\Meter\Annotation\Gauged`

适合数字有上下波动的统计。

例如：CPU 占用率统计。

| 参数名 | 类型 | 默认值  | 描述 |
| ------ | ------ | ------ | ------ |
| name | `string` | `imi.counted` | 指标名称 |
| recordFailuresOnly | `false` | `bool` | 是否只在抛出异常时记录 |
| tags | `array` | `[]` | 标签，键值数组 |
| description | `string` |  | 描述 |
| value | `string/float` | `{returnValue}` | 写入的值；`{returnValue}` 表示方法返回值；`{returnValue.xxx}` 表示方法返回值的属性值；`{params.0}` 表示方法参数值；`{params.0.xxx}` 表示方法参数值的属性值；也可以是固定的 `float` 值 |
| operation | `int` | `\Imi\Meter\Enum\GaugeOperation::SET` | 操作类型。设置`GaugeOperation::SET`；增加`GaugeOperation::INCREMENT`；减少`GaugeOperation::DECREMENT` |
| options | `array` | `[]` | 额外参数，每个驱动不同 |

###### @Timed

类名：`\Imi\Meter\Annotation\Timed`

耗时统计。

例如：方法执行耗时

| 参数名 | 类型 | 默认值  | 描述 |
| ------ | ------ | ------ | ------ |
| name | `string` | `imi.counted` | 指标名称 |
| tags | `array` | `[]` | 标签，键值数组 |
| description | `string` |  | 描述 |
| baseTimeUnit | `int` | `\Imi\Meter\Enum\TimeUnit::NANO_SECOND` | 基础时间单位，默认纳秒，可以使用 `\Imi\Meter\Enum\TimeUnit::XXX` 常量设置。 |
| options | `array` | `[]` | 额外参数，每个驱动不同 |

###### @Histogram

类名：`\Imi\Meter\Annotation\Histogram`

柱状图，一般人用不懂，如无特殊需求可以无视。

| 参数名 | 类型 | 默认值  | 描述 |
| ------ | ------ | ------ | ------ |
| name | `string` | `imi.counted` | 指标名称 |
| tags | `array` | `[]` | 标签，键值数组 |
| description | `string` |  | 描述 |
| buckets | `array` | `[]` | 桶，例如：`[100, 500, 1000]` |
| baseTimeUnit | `int` | `\Imi\Meter\Enum\TimeUnit::NANO_SECOND` | 基础时间单位，默认纳秒，可以使用 `\Imi\Meter\Enum\TimeUnit::XXX` 常量设置。 |
| value | `string/float` | `{returnValue}` | 写入的值；`{returnValue}` 表示方法返回值；`{returnValue.xxx}` 表示方法返回值的属性值；`{params.0}` 表示方法参数值；`{params.0.xxx}` 表示方法参数值的属性值；也可以是固定的 `float` 值 |
| options | `array` | `[]` | 额外参数，每个驱动不同 |

###### @Summary

类名：`\Imi\Meter\Annotation\Summary`

采样点分位图，一般人用不懂，如无特殊需求可以无视。

| 参数名 | 类型 | 默认值  | 描述 |
| ------ | ------ | ------ | ------ |
| name | `string` | `imi.counted` | 指标名称 |
| tags | `array` | `[]` | 标签，键值数组 |
| description | `string` |  | 描述 |
| percentile | `array` | `[]` | 百分位数，例如：`[0.01, 0.5, 0.99]` |
| baseTimeUnit | `int` | `\Imi\Meter\Enum\TimeUnit::NANO_SECOND` | 基础时间单位，默认纳秒，可以使用 `\Imi\Meter\Enum\TimeUnit::XXX` 常量设置。 |
| value | `string/float` | `{returnValue}` | 写入的值；`{returnValue}` 表示方法返回值；`{returnValue.xxx}` 表示方法返回值的属性值；`{params.0}` 表示方法参数值；`{params.0.xxx}` 表示方法参数值的属性值；也可以是固定的 `float` 值 |
| options | `array` | `[]` | 额外参数，每个驱动不同 |

**代码示例：**

```php
use Imi\Meter\Annotation\Gauged;
use Imi\Meter\Annotation\Histogram;
use Imi\Meter\Annotation\Summary;
use Imi\Meter\Annotation\Timed;
use Imi\Meter\Enum\TimeUnit;

#[Gauged(name: 'test_memory_usage', description: 'memory usage', tags: ['workerId' => '{returnValue.workerId}'], value: '{returnValue.memory}')]
public function recordMemoryUsage(): array
{
    return [
        'workerId' => Worker::getWorkerId(),
        'memory'   => memory_get_usage(),
    ];
}

#[Timed(name: 'test_timed', description: 'memory usage', baseTimeUnit: TimeUnit::MILLI_SECONDS)]
public function testTimed(): int
{
    $ms = mt_rand(10, 1000);
    usleep($ms * 1000);

    return $ms;
}

#[Timed(name: 'test_timed_histogram', description: 'memory usage', baseTimeUnit: TimeUnit::MILLI_SECONDS, options: ['histogram' => true])]
public function testTimedHistogram(): int
{
    $ms = mt_rand(10, 1000);
    usleep($ms * 1000);

    return $ms;
}

#[Histogram(name: 'test_histogram', baseTimeUnit: TimeUnit::MILLI_SECONDS)]
public function testHistogram(): int
{
    return mt_rand(10, 1000);
}

#[Summary(name: 'test_summary', baseTimeUnit: TimeUnit::MILLI_SECONDS)]
public function testSummary(): int
{
    return mt_rand(10, 1000);
}
```

##### 手动操作

```php
use \Imi\Meter\Facade\MeterRegistry;
use \Imi\Meter\Enum\TimeUnit;

$description = '我是描述';
$tags = ['result' => 'success'];

// counter
MeterRegistry::getDriverInstance()->counter('testCounterManual', $tags, $description)->increment();

// gauge
MeterRegistry::getDriverInstance()->gauge('testGaugeManual', $tags, $description)->record(114514);

// timer
$timer = MeterRegistry::getDriverInstance()->timer('testTimedManual', $tags, $description, TimeUnit::MILLI_SECONDS);
$timerSample = $timer->start();
usleep(mt_rand(10, 1000) * 1000);
$timerSample->stop($timer);

// timer Histogram
$timer = MeterRegistry::getDriverInstance()->timer('testTimedHistogramManual', $tags, $description, TimeUnit::MILLI_SECONDS, [
    'histogram' => true,
]);
$timerSample = $timer->start();
usleep(mt_rand(10, 1000) * 1000); // 你的耗时代码
$timerSample->stop($timer);

// Histogram
$value = 114514;
MeterRegistry::getDriverInstance()->histogram('testHistogramManual', $tags, $description)->record($value);

// Summary
$value = 114514;
MeterRegistry::getDriverInstance()->summary('testHistogramManual', $tags, $description)->record($value);
```
