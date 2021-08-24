# Summary

## 基础入门

* [序言](README.md)
* [介绍](base/intro.md)
* [Swoole 开发环境说明及安装教程](base/env.md)
* [开始一个新项目](base/new.md)
* [应用配置](base/config.md)
* [常见问题](base/qa.md)
* [v1.x-v2.x 不兼容改动](base/version/1-2.md)

## 框架核心

* [生命周期](core/lifeCycle.md)
* [容器](core/container.md)
* [Partial](core/partial.md)
* [事件](components/event/index.md)
  * [事件监听](components/event/index.md)
  * [事件列表](core/events.md)
* [中间件](core/middleware.md)
* [错误转为异常捕获](core/handleError.md)
* [内部进程间通讯](core/processCommunication.md)
* [Server 对象](core/server.md)
* [长连接分布式解决方案](core/long-connection-distributed.md)

## 注解

* [注入值注解](annotations/injectValue.md)
* [方法参数过滤器](annotations/filterArg.md)
* [编写自定义注解](annotations/annotation.md)
* [注解管理器](annotations/annotationManager.md)
* [注解相关问题](annotations/qa.md)

## 多容器

* [PHP-FPM](container/php-fpm.md)
* [Swoole](container/swoole.md)
  * [子服务器（单项目多端口多协议）](core/subServer.md)
  * [热更新](components/hotupdate/index.md)
  * [进程名称管理](core/processNameManager.md)
  * [工具类](utils/Coroutine.md)
    * [Coroutine](utils/Coroutine.md)
    * [Swoole](utils/Swoole.md)
    * [ChannelContainer](utils/ChannelContainer.md)
  * [命令行工具](dev/process-swoole.md)
    * [进程工具](dev/process-swoole.md)
  * [事件列表](container/swoole/events.md)
* [Workerman](container/workerman.md)
  * [服务器配置](container/workerman/serverConfig.md)
  * [命令行工具](dev/process-workerman.md)
    * [进程工具](dev/process-workerman.md)
  * [事件列表](container/workerman/events.md)

## Http 服务

* [路由](components/httpserver/route.md)
* [请求拦截](components/httpserver/intercept.md)
  * [AOP 拦截请求](components/httpserver/aop.md)
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
* [HTTPS 配置](components/server/ssl.md)
* [Swagger API 文档生成](dev/generate/swagger.md)

## WebSocket 服务

* [数据处理器](components/server/dataParser.md)
* [HTTP 路由](components/websocketServer/httpRoute.md)
* [HTTP 控制器](components/websocketServer/httpController.md)
* [WebSocket 路由](components/websocketServer/route.md)
* [WebSocket 控制器](components/websocketServer/websocketController.md)
* [中间件](components/websocketServer/middleware.md)
* [连接上下文](components/websocketServer/session.md)
* [连接分组](components/websocketServer/group.md)
* [断线重连](components/websocketServer/reconnect.md)
* [不使用中间件](components/websocketServer/noMiddleware.md)
* [WSS 配置](components/server/ssl.md)
* [向客户端推送数据（服务器工具类）](utils/Server.md)
* [WebSocket 客户端](components/websocketServer/client.md)

## TCP 服务

* [数据处理器](components/server/dataParser.md)
* [路由](components/tcpServer/route.md)
* [TCP 控制器](components/tcpServer/controller.md)
* [中间件](components/tcpServer/middleware.md)
* [连接上下文](components/websocketServer/session.md)
* [连接分组](components/websocketServer/group.md)
* [断线重连](components/websocketServer/reconnect.md)
* [不使用中间件](components/tcpServer/noMiddleware.md)
* [向客户端推送数据（服务器工具类）](utils/Server.md)

## UDP 服务

* [数据处理器](components/server/dataParser.md)
* [路由](components/udpServer/route.md)
* [UDP 控制器](components/udpServer/controller.md)
* [中间件](components/udpServer/middleware.md)
* [不使用中间件](components/udpServer/noMiddleware.md)

## MQTT

* [MQTT 服务端](components/mqtt/server.md)
* [MQTT 客户端](components/mqtt/client.md)

## RPC 服务

* [Hprose](components/rpc/hprose.md)
* [gRPC](components/rpc/grpc.md)

## 功能组件

* [配置读写](components/config/index.md)
* [连接池](components/pool/index.md)
* [数据库 (Db)](components/db/index.md)
  * [数据库驱动](components/db/index.md)
    * [MySQL](components/db/mysql.md)
    * [PostgreSQL](components/db/pgsql.md)
  * [数据库操作](components/db/index.md)
  * [SQL 监听](components/db/sqlListener.md)
* [ORM](components/orm/index.md)
  * [使用说明](components/orm/index.md)
  * [数据库表模型](components/orm/RDModel.md)
    * [使用方法](components/orm/RDModel.md)
    * [模型生成](dev/generate/model.md)
    * [表生成](dev/generate/table.md)
    * [模型事件](components/orm/RDModel/event.md)
    * [树形表模型](components/orm/TreeModel.md)
    * [模型软删除](components/orm/softDelete.md)
  * [模型关联](components/orm/relation/index.md)
    * [使用说明](components/orm/relation/index.md)
    * [一对一关联](components/orm/relation/oneToOne.md)
    * [一对多关联](components/orm/relation/oneToMany.md)
    * [多对多关联](components/orm/relation/manyToMany.md)
    * [多态一对一关联](components/orm/relation/polymorphicOneToOne.md)
    * [多态一对多关联](components/orm/relation/polymorphicOneToMany.md)
    * [多态多对多关联](components/orm/relation/polymorphicManyToMany.md)
    * [前置和后置事件](components/orm/relation/events.md)
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
* [请求上下文代理 (RequestContextProxy)](components/requestContextProxy/index.md)
* [Lock 锁](components/lock/index.md)
  * [使用方法](components/lock/index.md)
  * [RedisLock](components/lock/redis.md)
  * [AtomicLock](components/lock/atomic.md)
* [后台任务](components/task/index.md)
* [定时任务](components/task/cron.md)
* [AOP](components/aop/index.md)
* [进程](components/process/index.md)
* [进程池-Swoole](components/process-pool/swoole.md)
* [进程池-imi](components/process-pool/imi.md)
* [定时器](components/timer/index.md)
* [雪花算法发号器](components/snowflake.md)
* [imi 组件列表一览](components/list.md)
  * [MQTT](components/mqtt/server.md)
  * [gRPC](components/rpc/grpc.md)
  * [Hprose](components/rpc/hprose.md)
  * [消息队列](components/mq/redis.md)
  * [AMQP](components/mq/amqp.md)
  * [Kafka](components/mq/kafka.md)
  * [JWT](components/httpserver/jwt.md)
  * [权限控制](components/access-control.md)
  * [Smarty 模版引擎](components/smarty.md)
  * [限流](components/rate-limit.md)
  * [跨进程变量共享](components/shared-memory.md)
  * [雪花算法发号器](components/snowflake.md)
  * [Swagger API 文档生成](dev/generate/swagger.md)
  * [Swoole Tracker](components/swoole-tracker.md)

## 消息队列

* [Redis](components/mq/redis.md)
* [RabbitMQ(AMQP)](components/mq/amqp.md)
* [Kafka](components/mq/kafka.md)

## 数据结构

* [通用数据结构](components/struct/enum.md)
  * [Enum(枚举)](components/struct/enum.md)
  * [ArrayData(数组数据)](components/struct/ArrayData.md)
  * [LazyArrayObject(智能数组对象)](components/struct/LazyArrayObject.md)
  * [ArrayList(数组列表)](components/struct/ArrayList.md)
  * [FilterableList(过滤器列表)](components/struct/FilterableList.md)
* [Swoole 数据结构](components/struct/atomic.md)
  * [Atomic(原子性)](components/struct/atomic.md)
  * [Channel(通道)](components/struct/co-channel.md)
  * [MemoryTable(内存表)](components/struct/memory-table.md)

## 通用工具类

* [App 类](utils/app.md)
* [全局函数](utils/functions.md)
* [Imi](utils/Imi.md)
* [Worker](utils/Worker.md)
* [ArrayUtil](utils/ArrayUtil.md)
* [Bit](utils/Bit.md)
* [ClassObject](utils/ClassObject.md)
* [File](utils/File.md)
* [ObjectArrayHelper](utils/ObjectArrayHelper.md)
* [Random](utils/Random.md)
* [Text](utils/Text.md)
* [Pagination](utils/Pagination.md)
* [DateTime](utils/DateTime.md)
* [KVStorage](utils/KVStorage.md)
* [ServerManager](utils/ServerManager.md)
* [服务器工具类](utils/Server.md)

## 开发工具

* [介绍](dev/intro.md)
* [生成工具](dev/generate.md)
  * [模型生成](dev/generate/model.md)
  * [表生成](dev/generate/table.md)
  * [控制器生成](dev/generate/controller.md)
* [自己动手开发命令行工具](dev/tool.md)

## 进阶开发

* [断点调试](adv/debug.md)
* [性能优化](adv/performance.md)
* [帮助 imi 变得更好](adv/devp.md)

## 运行环境

* [Docker](production/docker.md)
* [守护进程](production/daemon.md)

## Swoole 协程化组件

* [Guzzle](other/guzzle.md)
* [ElasticSearch](other/elasticsearch.md)
* [YurunHttp](other/yurunhttp.md)
* [第三方授权 SDK](other/yurun-oauth-login.md)
* [第三方支付 SDK](other/paysdk.md)
