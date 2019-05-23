# 介绍

IMI 是基于 Swoole 开发的协程 PHP 开发框架，完美支持 Http、WebSocket、TCP、UDP 开发，拥有常驻内存、协程异步非阻塞IO等优点。

IMI 框架文档丰富，上手容易，致力于让开发者跟使用传统 MVC 框架一样顺手。

IMI 框架底层开发使用了强类型，易维护，性能更强。支持 Aop ，支持使用注解和配置文件注入，完全遵守 PSR-3、4、7、11、15、16 标准规范。

框架的扩展性强，开发者可以根据实际需求，自行开发相关驱动进行扩展。不止于框架本身提供的功能和组件！

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

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
