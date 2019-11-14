# 最大请求执行时间

在 `php-fpm` 中有 `max_execution_time` 这个选项，用来限定请求最大执行时间。

imi 提供了一个中间件，用以支持设置最大请求执行时间，如果超时可以做提前返回结果的处理。

## 使用方法

### 启用

在服务器配置 `beans` 节中配置中间件`ExecuteTimeoutMiddleware`：

```php
[
    'HttpDispatcher'    =>    [
        'middlewares'    =>    [
            'ExecuteTimeoutMiddleware',
        ],
    ],
]
```

### 超时时间设置

在服务器配置 `beans` 节中配置中间件`ExecuteTimeoutMiddleware`：

```php
[
    'ExecuteTimeoutMiddleware' =>  [
        'maxExecuteTime'    =>  3000,
        'handler'           =>  \Imi\Server\Http\Error\ExecuteTimeoutHandler::class,
    ],
]
```

上面的 handler 为 imi 内置的处理器，会返回一个错误状态码。

### 自定义超时处理器

定义一个类，实现 `Imi\Server\Http\Error\IExecuteTimeoutHandler`接口。

实现方法：`public function handle(Request $request, Response $response)`

> 目前 Swoole 不允许强行停止正在执行的协程，所以只是提前响应内容，而并不是中断任务执行，请知晓。
