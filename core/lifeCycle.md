# 生命周期

imi 是基于 Swoole 开发的框架，所以最好先了解 Swoole，以便了解各种名词概念。

## 框架生命周期

框架的生命周期大致分为以下几个阶段：

### master 进程

* 初始化框架
* 扫描注解，构建运行时缓存
* 初始化 Swoole 中的 Memory 模块
* 加载项目配置
* 创建服务器对象
* 启动服务器

### worker 进程

* Swoole WorkerStart 事件
* 清除容器中的类缓存文件
* 加载运行时缓存
* 初始化日志相关
* 初始化连接池
* 初始化缓存
* 初始化路由
* 触发项目初始化事件（worker 0 第一次启动）

## 请求生命周期

imi 支持 http、websocket、tcp、udp，但其实都大同小异，都使用了监听事件+中间件的套路，这里以 http 为例说明。

* Swoole Request 事件
* 创建请求上下文 RequestContext
* 调度器（HttpDispatcher）执行中间件
* 匹配路由
* 控制器对象->动作方法()
* Response 响应