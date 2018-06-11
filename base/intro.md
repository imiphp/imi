# 介绍

IMI 是基于 Swoole 开发的协程 PHP 开发框架，拥有常驻内存、协程异步非阻塞IO等优点。传统 MVC 框架开发者可以依靠我们完善的文档轻松上手，IMI致力于为业务开发者提供强力驱动。

IMI 框架底层开发使用了强类型，支持 Aop ，支持使用注解和配置文件注入，完全遵守 PSR-3、4、7、11、15、16 标准规范。

框架的扩展性强，开发者可以根据框架提供的接口，自行开发相关驱动进行扩展。不止于框架本身提供的功能和组件！

框架暂未实战验证，难免有 BUG，无能力阅读修改源代码的请暂时慎重选择。等待我们实战项目开发并完善稳定后再使用！

同时欢迎有志之士加入我们，一起开发完善！

QQ群：74401592 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://shang.qq.com/wpa/qunwpa?idkey=e2e6b49e9a648aae5285b3aba155d59107bb66fde02e229e078bd7359cac8ac3)，如有问题会有人解答和修复。

### 功能组件

- [x] Aop (同时支持注解和配置文件)
- [x] Container (PSR-11)
- [x] 注解
- [x] 全局事件/类事件
- [x] HttpServer
- [x] HttpRequest/HttpResponse (PSR-7)
- [x] Http 中间件、注解路由、配置文件路由 (PSR-15)
- [x] Session (File + Redis)
- [x] View
- [x] 日志 (PSR-3 / File + Console)
- [x] 缓存 (PSR-16 / File + Redis)
- [x] Redis (同步 + 协程) 连接池
- [x] 协程 MySQL 连接池
- [x] PDO 连接池
- [ ] 协程 PostgreSQL 连接池
- [x] Db 连贯操作
- [x] Model ORM
- [x] Task 异步任务
- [ ] 命令行开发辅助工具
- [ ] 图形化管理工具
- [ ] 项目热更新
- [ ] RPC 远程调用
- [ ] WebSocket 服务器相关……
- [ ] TCP 服务器相关……
