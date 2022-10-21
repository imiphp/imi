# Prometheus

Prometheus 是云原生计算基金会的一个项目，是一个系统和服务监控系统。它以给定的时间间隔从配置的目标收集指标，评估规则表达式，显示结果，并能在观察到指定条件时触发警报。

普罗米修斯区别于其他指标和监控系统的特点是。

* 多维数据模型（时间序列由指标名称和一组键/值维度定义）
* PromQL，一种强大而灵活的查询语言，以利用这种维度
* 不依赖分布式存储；单个服务器节点是自主的
* 用于收集时间序列的HTTP拉动模型
* 通过中间网关支持批量作业的时间序列推送
* 通过服务发现或静态配置来发现目标
* 支持多种模式的图形和仪表盘制作
* 支持分层和水平联合

项目地址：<https://github.com/prometheus/prometheus>

imi-prometheus：<https://github.com/imiphp/imi-prometheus>

## 安装

`composer require imiphp/imi-prometheus:~2.1.0`

## 使用说明

### 配置

首先需要配置 Redis 连接，可以参考文档，这里不再赘述。

**配置监控指标：**

`@app.beans`：

```php
[
    'MeterRegistry' => [
        'driver'  => \Imi\Prometheus\PrometheusMeterRegistry::class,
        'options' => [
            'adapter' => [
                'class'   => \Imi\Prometheus\Storage\Redis::class,
                'options' => [
                    // 'poolName' => null, // 连接池名称，如果为 null 则使用默认 Redis 连接池
                    // 'prefix' => 'PROMETHEUS_', // 键名前缀
                ],
            ],
        ],
    ],
]
```

### 使用

#### 注解

##### @Counted

计数统计，适合只累加，不减少的统计数据类型。

例如：访问次数统计。

| 参数名 | 类型 | 默认值  | 描述 |
| ------ | ------ | ------ | ------ |
| name | `string` | `imi.counted` | 指标名称 |
| recordFailuresOnly | `false` | `bool` | 是否只在抛出异常时记录 |
| tags | `array` | `[]` | 标签，键值数组 |
| description | `string` |  | 描述 |
| options | `array` | `[]` | 额外参数，每个驱动不同 |

##### @Gauged

适合数字有上下波动的统计。

例如：CPU 占用率统计。

| 参数名 | 类型 | 默认值  | 描述 |
| ------ | ------ | ------ | ------ |
| name | `string` | `imi.counted` | 指标名称 |
| recordFailuresOnly | `false` | `bool` | 是否只在抛出异常时记录 |
| tags | `array` | `[]` | 标签，键值数组 |
| description | `string` |  | 描述 |
| value | `string\|float` | `{returnValue}` | 写入的值；`{returnValue}` 表示方法返回值；`{returnValue.xxx}` 表示方法返回值的属性值；`{params.0}` 表示方法参数值；`{params.0.xxx}` 表示方法参数值的属性值；也可以是固定的 `float` 值 |
| operation | `int` | `\Imi\Meter\Enum\GaugeOperation::SET` | 操作类型。设置`GaugeOperation::SET`；增加`GaugeOperation::INCREMENT`；减少`GaugeOperation::DECREMENT` |
| options | `array` | `[]` | 额外参数，每个驱动不同 |

##### @Timed

耗时统计。

例如：方法执行耗时

| 参数名 | 类型 | 默认值  | 描述 |
| ------ | ------ | ------ | ------ |
| name | `string` | `imi.counted` | 指标名称 |
| tags | `array` | `[]` | 标签，键值数组 |
| description | `string` |  | 描述 |
| baseTimeUnit | `int` | `\Imi\Meter\Enum\TimeUnit::NANO_SECOND` | 基础时间单位，默认纳秒，可以使用 `\Imi\Meter\Enum\TimeUnit::XXX` 常量设置。 |
| options | `array` | `[]` | 额外参数，每个驱动不同 |

`options` 在普罗米修斯的特定配置：

```php
[
    'histogram' => true, // 设置为柱状图，否则默认为 Summary
    'buckets' => [], // 桶，仅柱状图
    'maxAgeSeconds' => 600, // Summary 最大生存时间
    'percentile' => [], // Summary 百分位
]
```

##### @Histogram

柱状图，一般人用不懂，如无特殊需求可以无视。

| 参数名 | 类型 | 默认值  | 描述 |
| ------ | ------ | ------ | ------ |
| name | `string` | `imi.counted` | 指标名称 |
| tags | `array` | `[]` | 标签，键值数组 |
| description | `string` |  | 描述 |
| buckets | `array` | `[]` | 桶，例如：`[100, 500, 1000]` |
| baseTimeUnit | `int` | `\Imi\Meter\Enum\TimeUnit::NANO_SECOND` | 基础时间单位，默认纳秒，可以使用 `\Imi\Meter\Enum\TimeUnit::XXX` 常量设置。 |
| value | `string\|float` | `{returnValue}` | 写入的值；`{returnValue}` 表示方法返回值；`{returnValue.xxx}` 表示方法返回值的属性值；`{params.0}` 表示方法参数值；`{params.0.xxx}` 表示方法参数值的属性值；也可以是固定的 `float` 值 |
| options | `array` | `[]` | 额外参数，每个驱动不同 |

##### @Summary

采样点分位图，一般人用不懂，如无特殊需求可以无视。

| 参数名 | 类型 | 默认值  | 描述 |
| ------ | ------ | ------ | ------ |
| name | `string` | `imi.counted` | 指标名称 |
| tags | `array` | `[]` | 标签，键值数组 |
| description | `string` |  | 描述 |
| percentile | `array` | `[]` | 百分位数，例如：`[0.01, 0.5, 0.99]` |
| baseTimeUnit | `int` | `\Imi\Meter\Enum\TimeUnit::NANO_SECOND` | 基础时间单位，默认纳秒，可以使用 `\Imi\Meter\Enum\TimeUnit::XXX` 常量设置。 |
| value | `string\|float` | `{returnValue}` | 写入的值；`{returnValue}` 表示方法返回值；`{returnValue.xxx}` 表示方法返回值的属性值；`{params.0}` 表示方法参数值；`{params.0.xxx}` 表示方法参数值的属性值；也可以是固定的 `float` 值 |
| options | `array` | `[]` | 额外参数，每个驱动不同 |

**代码示例：**

```php
use Imi\Meter\Annotation\Gauged;
use Imi\Meter\Annotation\Histogram;
use Imi\Meter\Annotation\Summary;
use Imi\Meter\Annotation\Timed;
use Imi\Meter\Enum\TimeUnit;

/**
 * @Gauged(name="test_memory_usage", description="memory usage", tags={"workerId"="{returnValue.workerId}"}, value="{returnValue.memory}")
 */
public function recordMemoryUsage(): array
{
    return [
        'workerId' => Worker::getWorkerId(),
        'memory'   => memory_get_usage(),
    ];
}

/**
 * @Timed(name="test_timed", description="memory usage", baseTimeUnit=TimeUnit::MILLI_SECONDS, options={"quantiles"={0.1, 0.5, 0.99}})
 */
public function testTimed(): int
{
    $ms = mt_rand(10, 1000);
    usleep($ms * 1000);

    return $ms;
}

/**
 * @Timed(name="test_timed_histogram", description="memory usage", baseTimeUnit=TimeUnit::MILLI_SECONDS, options={"histogram"=true, "buckets"={50, 100, 300, 600, 800, 1000}})
 */
public function testTimedHistogram(): int
{
    $ms = mt_rand(10, 1000);
    usleep($ms * 1000);

    return $ms;
}

/**
 * @Histogram(name="test_histogram", baseTimeUnit=TimeUnit::MILLI_SECONDS, buckets={50, 100, 300, 600, 800, 1000})
 */
public function testHistogram(): int
{
    return mt_rand(10, 1000);
}

/**
 * @Summary(name="test_summary", baseTimeUnit=TimeUnit::MILLI_SECONDS, percentile={0.1, 0.5, 0.99})
 */
public function testSummary(): int
{
    return mt_rand(10, 1000);
}
```

#### 手动操作

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
    'buckets'   => [100, 500, 1500],
]);
$timerSample = $timer->start();
usleep(mt_rand(10, 1000) * 1000); // 你的耗时代码
$timerSample->stop($timer);

// Histogram
$value = 114514;
$buckets = [100, 500, 1500];
MeterRegistry::getDriverInstance()->histogram('testHistogramManual', $tags, $description, $buckets)->record($value);

// Summary
$value = 114514;
$percentile = [0.1, 0.5, 0.99];
MeterRegistry::getDriverInstance()->summary('testHistogramManual', $tags, $description, $percentile)->record($value);
```
