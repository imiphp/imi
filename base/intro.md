# 介绍

imi 是基于 PHP 协程应用开发框架，它支持 HttpApi、WebSocket、TCP、UDP 应用开发。

由 Swoole 提供强力驱动，Swoole 拥有常驻内存、协程非阻塞 IO 等特性。

框架遵守 PSR 标准规范，提供 AOP、注解、连接池、请求上下文管理、ORM模型等常用组件。

imi 的模型支持关联关系的定义，增删改查一把梭！

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题，负责的宇润全程手把手解决。

### 为什么要用 Swoole？

https://wiki.swoole.com/

https://my.oschina.net/yurun/blog/1831238

https://my.oschina.net/yurun/blog/3034196

### 功能组件

- [x] Server (Http/Websocket/Tcp/Udp)
- [x] 容器 (PSR-11)
- [x] Aop 注入
- [x] Http 中间件 (PSR-15)
- [x] MySQL 连接池 (协程&同步，主从，负载均衡)
- [x] Redis 连接池 (协程&同步，负载均衡)
- [x] Db 连贯操作
- [x] 关系型数据库 模型
- [x] 跨进程共享内存表 模型
- [x] Redis 模型
- [x] 日志 (PSR-3 / File + Console)
- [x] 缓存 (PSR-16 / File + Redis)
- [x] 验证器 (Valitation)
- [x] Task 异步任务
- [x] 进程/进程池
- [x] 命令行开发辅助工具
- [x] 业务代码热更新
