# OpenTracing

[TOC]

OpenTracing 是一种分布式系统链路跟踪的设计原则、规范、标准。

**支持的中间件：**

* [x] Zipkin

* [x] Jaeger

* [ ] Skywalking

**imi-opentracing:** <https://github.com/imiphp/imi-opentracing>

## 配置

`@app.beans`:

```php
'Tracer'   => [
    // 不同驱动配置不同，请往下翻，看具体的驱动配置说明
],

// 数据库调用追踪
'DbTracer' => [
    'enable' => true, // 是否启用，默认 false
],

// Redis 调用追踪
'RedisTracer' => [
    'enable' => true, // 是否启用，默认 false
],
```

**启用 HTTP 请求追踪：**

服务器配置的 `beans` 中：

```php
[
    'HttpDispatcher'    => [
        'middlewares'    => [
            \Imi\OpenTracing\Middleware\HttpRequestTracingMiddleware::class, // 将这个中间件放到最前面
            // 这里是你的其它中间件
        ],
    ],
]
```

## 使用

开启追踪会造成性能损耗，请根据实际需要在生产环境中使用！

### 追踪方法调用（注解）

> 仅使用容器实例化的对象方法可被追踪

```php
<?php

declare(strict_types=1);

namespace app\Service;

use Imi\OpenTracing\Annotation\Tag;
use Imi\OpenTracing\Annotation\Trace;

class TestService
{
    /**
     * @Trace("add")
     * @Tag(key="method.params.a", value="{params.0}")
     * @Tag(key="method.params.b", value="{params.1}")
     * @Tag(key="method.returnValue", value="{returnValue}")
     * @Tag(key="method.message", value="{params.0}+{params.1}={returnValue}")
     *
     * @param int|float $a
     * @param int|float $b
     *
     * @return int|float
     */
    public function add($a, $b)
    {
        return $a + $b;
    }
}
```

上面的代码追踪的操作是 `add`，`@Trace` 也可以不指定操作名称，默认是：`类名::方法名()`

`@Tag` 是可选的，记录一些标签数据。`{params.0}` 就是代入方法的第一个参数值；`{returnValue}` 是代入方法的返回值。

你甚至可以使用 `params.0.id`、`returnValue.name` 类似这种写法，获取类型为数组或对象的属性值。

### 手动追踪

**在当前服务中增加追踪：**

```php
use Imi\OpenTracing\Facade\Tracer;

// 开始
$scope = Tracer::startActiveSpan('write1');

// ...
// 这里可以做一些事情

// 结束
$scope->close();
```

**用一个服务名追踪：**

```php
use Imi\OpenTracing\Facade\Tracer;

// 创建 Tracer
$tracer = Tracer::createTracer('redis');
// 开始
$scope1 = TracerUtil::startRootActiveSpan($tracer, 'test1');

// ...
// 这里可以做一些事情

// 可以继续在 test1 下增加追踪
$scope2 = $tracer->startActiveSpan('test1-1');
// ...
// 这里可以做一些事情
// 结束 test1-1
$scope2->close();

// ...
// 这里可以做一些事情

// 结束
$scope1->close();
$tracer->flush();
```

### 异常类忽略追踪

在异常类上加上 `@IgnoredException` 注解，捕获到该注解时不会认为错误

```php
<?php

declare(strict_types=1);

namespace app\Exception;

use Imi\OpenTracing\Annotation\IgnoredException;
use RuntimeException;

/**
 * @IgnoredException
 */
class GGException extends RuntimeException
{
}
```

## 驱动

### Jaeger

Jaeger 是受 Dapper 和 OpenZipkin 的启发，由 Uber Technologies 创建的分布式追踪平台，现已捐赠给云原生计算基金会。它可用于监视基于微服务的分布式系统：

* 分布式上下文传播
* 分布式交易监控
* 根本原因分析
* 服务依赖分析
* 性能 / 延迟优化

#### 安装 Jaeger 所需组件

`composer require imiphp/imi-opentracing:~2.1.0 jonahgeorge/jaeger-client-php`

#### Jaeger 配置

`@app.beans`:

```php
'Tracer'   => [
    // 驱动类
    'driver'  => \Imi\OpenTracing\Driver\JaegerTracerDriver::class,
    // 配置项
    'options' => [
        // 服务名称，自行修改
        'serviceName' => 'imi-opentracing',
        // 客户端配置
        'config'      => [
            // 采样配置
            // 每次都采样，建议测试用
            'sampler' => [
                'type'  => Jaeger\SAMPLER_TYPE_CONST,
                'param' => true,
            ],

            // 概率采样，建议生产用
            // 'sampler' => [
            //     'type' => Jaeger\SAMPLER_TYPE_PROBABILISTIC,
            //     'param' => 0.5, // float [0.0, 1.0]
            // ],

            // 限流采样，建议生产用
            // 'sampler' => [
            //     'type' => Jaeger\SAMPLER_TYPE_RATE_LIMITING,
            //     'param' => 100 // 每秒最大追踪次数
            //     'cache' => [
            //         'currentBalanceKey' => 'rate.currentBalance' // string
            //         'lastTickKey' => 'rate.lastTick' // string
            //     ]
            // ],

            // 连接配置
            'local_agent' => [
                'reporting_host' => '127.0.0.1', // 主机名
                // 端口
                // ZIPKIN_OVER_COMPACT_UDP 默认：5775
                // JAEGER_OVER_BINARY_UDP 默认：6832
                // JAEGER_OVER_BINARY_HTTP 默认：14268
                'reporting_port' => 5775,
            ],
            // 通信协议
            'dispatch_mode' => \Jaeger\Config::ZIPKIN_OVER_COMPACT_UDP, // Zipkin.thrift + UDP，默认
            'dispatch_mode' => \Jaeger\Config::JAEGER_OVER_BINARY_UDP, // Jaeger.thrift + UDP
            'dispatch_mode' => \Jaeger\Config::JAEGER_OVER_BINARY_HTTP, //Jaeger.thrift + HTTP

            'logging' => true,
            "tags" => [
                // 前缀 prefix. 只在 JAEGER_OVER_HTTP, JAEGER_OVER_BINARY 中起作用。
                // 否则它将被显示为简单的全局标签
                "process.process-tag-key-1" => "process-value-1", // 所有带有`process.`前缀的标签都归入进程部分。
                "process.process-tag-key-2" => "process-value-2", //所有带有`process.`前缀的标签都进入进程部分。
                "global-tag-key-1" => "global-tag-value-1", // 这个标签将被附加到所有的 span 中。
                "global-tag-key-2" => "global-tag-value-2", // 这个标签将被附加到所有的 span 中。
            ],
        ],
    ],
],
```

#### Jaeger Docker

**docker-compose.yml:**

```yml
version: "2"
services:
    jaeger:
        image: jaegertracing/all-in-one:1.38
        container_name: jaeger
        ports:
            - "6831:6831"
            - "6832:6832"
            - "5778:5778"
            - "16686:16686"
            - "14268:14268"
            - "9411:9411"
```

> 仅供开发调试用

### Zipkin

Zipkin是 Twitter 的一个开源项目，基于 Google Dapper 实现。

可以使用它来收集各个服务器上请求链路的跟踪数据，并通过它提供的 REST API 接口来辅助我们查询跟踪数据以实现对分布式系统的监控程序，从而及时地发现系统中出现的延迟升高问题并找出系统性能瓶颈的根源。

除了面向开发的API接口之外，它也提供了方便的 UI 组件帮助我们直观的搜索跟踪信息和分析请求链路明细，比如：可以查询某段时间内各用户请求的处理时间等。

#### 安装 Zipkin 所需组件

`composer require imiphp/imi-opentracing:~2.1.0 jcchavezs/zipkin-opentracing`

#### Zipkin 配置

`@app.beans`:

```php
'Tracer'   => [
    // 驱动类
    'driver'  => \Imi\OpenTracing\Driver\ZipkinTracerDriver::class,
    // 配置项
    'options' => [
        // 服务名称，自行修改
        'serviceName' => 'imi-opentracing',
        // 客户端配置
        'config'      => [
            // 采样配置
            'sampler' => [
                // 采样类创建方法
                // 每次都采样，建议测试用
                'creator'       => '\Zipkin\Samplers\BinarySampler::createAsAlwaysSample',
                'creatorParams' => [true],

                // 概率采样，建议生产用
                // 'creator'       => '\Zipkin\Samplers\PercentageSampler::create',
                // 'creatorParams' => [0.5], // float [0.0, 1.0]
            ],
            // 连接配置
            'reporter' => [
                'endpoint_url'   => 'http://localhost:9411/api/v2/spans',
            ],
        ],
    ],
],
```

#### Zipkin Docker

**docker-compose.yml:**

```yml
version: '2.4'
services:
  zipkin:
    image: openzipkin/zipkin:2
    container_name: zipkin
    environment:
      - STORAGE_TYPE=mem
    ports:
      # Port used for the Zipkin UI and HTTP Api
      - 9412:9411
```

> 仅供开发调试用
