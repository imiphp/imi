# 单端口支持 WebSocket+Http

[toc]

imi 框架原生支持在同一个项目中，使用一个服务器监听多个端口和协议，例如你要开发一个聊天系统，需要同时支持 HTTP 和 WebSocket 协议，imi 框架可以方便地实现这个功能。

需要将服务器声明为 WebSocket 类型，主服务器或子服务器均可。在服务器配置文件中的 `beans` 配置 `HttpDispatcher` 和 `WebSocketDispatcher` 即可实现。这样，在同一个端口下，即可同时支持 HTTP 和 WebSocket 协议，实现代码复用和互相调用。

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
