# 单端口支持 WebSocket+Http

[toc]

Swoole 容器可以一个端口同时支持 WebSocket + Http 协议。

必须将服务器声明为 WebSocket 类型，主服务器或子服务器不限。

在服务器配置文件中的 `beans` 配置 `HttpDispatcher` 和 `WebSocketDispatcher` 即可。

```php
'HttpDispatcher'    =>    [
    'middlewares'    =>    [
        \Imi\Server\Http\Middleware\RouteMiddleware::class,
    ],
],
'WebSocketDispatcher'    =>    [
    'middlewares'    =>    [
        \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
    ],
],
```
