# Summary

## 基础入门

* [序言](README.md)
* [介绍](base/intro.md)
* [环境要求](base/env.md)
* [开始一个新项目](base/new.md)
* [配置文件](base/config.md)

## 框架核心

* [生命周期](core/lifeCycle.md)
* [容器](core/container.md)
* [Partial](core/partial.md)
* [事件](core/events.md)
* [中间件](core/middleware.md)
* [进程名称管理](core/processNameManager.md)
* [错误转为异常捕获](core/handleError.md)
* [子服务器（单项目多端口多协议）](core/subServer.md)

## Http 服务

* [高性能 Http 服务](components/httpserver/coServer.md)
* [路由](components/httpserver/route.md)
* [中间件](components/httpserver/middleware.md)
* [控制器](components/httpserver/controller.md)
* [RESTful](components/httpserver/restful.md)
* [Session](components/httpserver/session.md)
* [JWT](components/httpserver/jwt.md)
* [视图](components/httpserver/view.md)
* [错误异常处理](components/httpserver/error.md)
* [404处理](components/httpserver/404.md)
* [HTTP 验证器](components/httpserver/validation.md)
* [超全局变量](components/httpserver/superGlobals.md)
* [请求最大执行时间](components/httpserver/maxExecuteTime.md)
* [Http2](components/httpserver/http2.md)
* [跨域和 OPTIONS 请求](components/httpserver/cros.md)

## WebSocket 服务

* [HTTP 路由](components/websocketServer/httpRoute.md)
* [HTTP 控制器](components/websocketServer/httpController.md)
* [WebSocket 路由](components/websocketServer/route.md)
* [WebSocket 控制器](components/websocketServer/websocketController.md)
* [中间件](components/websocketServer/middleware.md)
* [连接上下文](components/websocketServer/session.md)
* [连接分组](components/websocketServer/group.md)
* [不使用中间件](components/websocketServer/noMiddleware.md)

## TCP 服务

* [路由](components/tcpServer/route.md)
* [TCP 控制器](components/tcpServer/controller.md)
* [中间件](components/tcpServer/middleware.md)
* [连接上下文](components/tcpServer/session.md)
* [连接分组](components/tcpServer/group.md)
* [不使用中间件](components/tcpServer/noMiddleware.md)

## UDP 服务

* [路由](components/udpServer/route.md)
* [UDP 控制器](components/udpServer/controller.md)
* [中间件](components/udpServer/middleware.md)
* [不使用中间件](components/udpServer/noMiddleware.md)

## RPC 服务

* [Hprose](components/rpc/hprose.md)
* [gRPC](components/rpc/grpc.md)

## 消息队列

* [AMQP](components/mq/amqp.md)
* [RabbitMQ](components/mq/amqp.md)

## 功能组件

* [配置读写](components/config/index.md)
* [连接池](components/pool/index.md)
* [数据库操作](components/db/index.md)
* [ORM](components/orm/index.md)
  * [使用说明](components/orm/index.md)
  * [数据表模型](components/orm/RDModel.md)
    * [使用方法](components/orm/RDModel.md)
    * [树形表模型](components/orm/TreeModel.md)
    * [模型事件](components/orm/RDModel/event.md)
  * [模型关联](components/orm/relation/index.md)
    * [使用说明](components/orm/relation/index.md)
    * [一对一关联](components/orm/relation/oneToOne.md)
    * [一对多关联](components/orm/relation/oneToMany.md)
    * [多对多关联](components/orm/relation/manyToMany.md)
    * [多态一对一关联](components/orm/relation/polymorphicOneToOne.md)
    * [多态一对多关联](components/orm/relation/polymorphicOneToMany.md)
    * [多态多对多关联](components/orm/relation/polymorphicManyToMany.md)
* [Redis](components/redis/index.md)
* [Redis 模型](components/orm/RedisModel.md)
* [内存表模型](components/orm/MemoryTableModel.md)
* [缓存](components/cache/index.md)
  * [使用说明](components/cache/index.md)
  * [File](components/cache/file.md)
  * [Redis](components/cache/redis.md)
  * [RedisHash](components/cache/redisHash.md)
* [日志](components/log/index.md)
* [验证器](components/validation/index.md)
* [门面 (Facade)](components/facade/index.md)
* [Lock 锁](components/lock/index.md)
  * [使用方法](components/lock/index.md)
  * [RedisLock](components/lock/redis.md)
  * [AtomicLock](components/lock/atomic.md)
* [事件监听](components/event/index.md)
* [后台任务](components/task/index.md)
* [定时任务](components/task/cron.md)
* [AOP](components/aop/index.md)
* [进程](components/process/index.md)
* [进程池-Swoole](components/process-pool/swoole.md)
* [进程池-imi](components/process-pool/imi.md)
* [热更新](components/hotupdate/index.md)
* [Phar 支持](components/phar/index.md)

## 数据结构

* [Atomic](components/struct/atomic.md)
* [Channel](components/struct/co-channel.md)
* [MemoryTable](components/struct/memory-table.md)
* [Enum](components/struct/enum.md)
* [ArrayData](components/struct/ArrayData.md)
* [LazyArrayObject](components/struct/LazyArrayObject.md)
* [ArrayList](components/struct/ArrayList.md)
* [FilterableList](components/struct/FilterableList.md)

## 工具类

* [App 类](utils/app.md)
* [全局函数](utils/functions.md)
* [Imi](utils/Imi.md)
* [Args](utils/Args.md)
* [ArrayUtil](utils/ArrayUtil.md)
* [Bit](utils/Bit.md)
* [ClassObject](utils/ClassObject.md)
* [Coroutine](utils/Coroutine.md)
* [File](utils/File.md)
* [ObjectArrayHelper](utils/ObjectArrayHelper.md)
* [Random](utils/Random.md)
* [Swoole](utils/Swoole.md)
* [Text](utils/Text.md)
* [Pagination](utils/Pagination.md)
* [DateTime](utils/DateTime.md)
* [KVStorage](utils/KVStorage.md)
* [ServerManage](utils/ServerManage.md)

## 注解

* [注入值注解](annotations/injectValue.md)
* [方法参数过滤器](annotations/filterArg.md)

## 命令行工具

* [介绍](dev/intro.md)
* [服务器工具](dev/server.md)
* [生成工具](dev/generate.md)
  * [模型生成](dev/generate/model.md)
  * [控制器生成](dev/generate/controller.md)
* [进程工具](dev/process.md)
* [自己动手开发命令行工具](dev/tool.md)

## 进阶开发

* [性能优化](adv/performance.md)
* [参与框架开发](adv/devp.md)

## 生产环境

* [Docker](production/docker.md)
* [守护进程](production/daemon.md)

## Swoole 协程化组件

* [Guzzle](other/guzzle.md)
* [ElasticSearch](other/elasticsearch.md)
* [YurunHttp](other/yurunhttp.md)
* [第三方授权 SDK](other/yurun-oauth-login.md)
* [第三方支付 SDK](other/paysdk.md)
