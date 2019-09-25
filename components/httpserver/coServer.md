# 高性能 Http 服务

`imi v1.0.12` 版本新增了一个 [`Swoole\Coroutine\Http\Server`](https://wiki.swoole.com/wiki/page/p-coroutine_http_server.html) 实现的协程服务器。需要 `Swoole 4.4+` 才可使用该特性。

该特性是可选的，不影响以前使用的服务器模式。

使用协程服务器特性，依靠 Linux 系统的端口重用机制，系统级的负载均衡，可以让你的多进程 Http 服务处理请求的能力得到提升。

使用 ab，本机->虚拟机（双核+2进程）压测`Hello World` 页面，相比之前的服务器模式大概有 **80%** 的性能提升。该数据仅供参考，不同配置、环境、业务代码跑出来的数字会有差距，但很明显，协程服务器可以实现更高性能的 Http 服务。

## 使用方式

我们通常使用 `vendor/bin/imi server/start` 来启动服务器

启动主服务器：`vendor/bin/imi server/start -name main`

启动子服务器：`vendor/bin/imi server/start -name 子服务器名`

指定进程数：

启动主服务器：`vendor/bin/imi server/start -name main -workerNum 4`

进程数参数是可以忽略的，优先读取配置文件中的 `@app.mainServer.configs.worker_num` 配置，如果该配置项不存在则使用 CPU 核心数作为进程数量。

## 优缺点比较

### 优点

* 👍高性能，Linux 系统级的负载均衡

* 💪高度可控，使用 imi 自研进程池维护 Worker 进程

* ☕无缝支持，业务代码无需做任何更改，即可享受到高性能

### 缺点

* 不支持 Task 特性

* 不支持监听多端口、多协议

### 总结

使用该特性，可以享受到高性能带来的好处，但是无法使用 Task，以及多端口多协议监听。

对于开发 Http API 的场景来讲，是非常适合用这个特性的。
