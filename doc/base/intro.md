# 介绍

imi 是一款支持长连接微服务分布式的 PHP 开发框架，它可以运行在 PHP-FPM、Swoole、Workerman 多种容器环境下。

imi 支持开发 Http 接口，以及 Http2、WebSocket、TCP、UDP、MQTT 等常驻内存服务。

imi 拥有丰富的功能组件，v2.x 版本内置了 2 个分布式长连接服务的解决方案。可以广泛应用于互联网、移动通信、企业软件、云计算、网络游戏、物联网（IOT）、车联网、智能家居等领域。使企业 IT 研发团队的效率大大提升，更加专注于开发创新产品。

> imi 第一个版本发布于 2018 年 6 月 21 日

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png)](https://jq.qq.com/?_wv=1027&k=5wXf4Zq "点击加群")，如有问题，负责的宇润全程手把手解决。

## 官方视频教程（完全免费）

imi 2.0 基础视频教程(免费连载中):

<https://space.bilibili.com/768718/channel/seriesdetail?sid=274078>
<https://www.zhihu.com/people/yurunsoft/zvideos>

imi 框架入门教程（免费11集全）<https://www.bilibili.com/video/av78158909>

imi 框架进阶教程——五子棋游戏开发(免费7集全)<https://space.bilibili.com/768718/channel/detail?cid=136926>

### 为什么要用 Swoole？

https://wiki.swoole.com/

https://my.oschina.net/yurun/blog/1831238

https://my.oschina.net/yurun/blog/3034196

### 核心组件

* Http、Http2、WebSocket、TCP、UDP、MQTT 服务器
* 分布式长连接解决方案（消息队列模式、网关模式）
* MySQL 连接池 (主从+负载均衡)
* Redis 连接池 (主从+负载均衡)
* 超好用的 ORM (Db、Redis、Tree)
* 毫秒级热更新
* AOP
* Bean 容器
* 缓存 (Cache)
* 配置读写 (Config)
* 枚举 (Enum)
* 事件 (Event)
* 门面 (Facade)
* 验证器 (Validate)
* 锁 (Lock)
* 日志 (Log)
* 异步任务 (Task)
