<p align="center">
    <a href="https://www.imiphp.com" target="_blank">
        <img src="" alt="imi" />
    </a>
</p>

![Latest Version](https://img.shields.io/packagist/v/yurunsoft/imi.svg?style=for-the-badge)
![Travis](https://img.shields.io/travis/USER/REPO.svg?style=for-the-badge)
[![Php Version](https://img.shields.io/badge/php-%3E=7.0-brightgreen.svg?style=for-the-badge)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=2.2.0-brightgreen.svg?style=for-the-badge)](https://github.com/swoole/swoole-src)
[![Hiredis Version](https://img.shields.io/badge/hiredis-%3E=0.1-brightgreen.svg?style=for-the-badge)](https://github.com/redis/hiredis)
[![IMI Doc](https://img.shields.io/badge/docs-passing-green.svg?style=for-the-badge)](https://doc.imiphp.com)
[![IMI License](https://img.shields.io/hexpm/l/plug.svg?style=for-the-badge)](https://github.com/Yurunsoft/imi/blob/master/LICENSE)

## 介绍

IMI 是基于 Swoole 开发的协程 PHP 开发框架，拥有常驻内存、协程异步非阻塞IO等优点。传统 MVC 框架开发者可以依靠我们完善的文档轻松上手，IMI致力于为业务开发者提供强力驱动。

IMI 框架底层开发使用了强类型，支持 Aop ，支持使用注解和配置文件注入，完全遵守 PSR-3、4、7、11、15、16 标准规范。

框架的扩展性强，开发者可以根据框架提供的接口，自行开发相关驱动进行扩展。不止于框架本身提供的功能和组件！

框架暂未实战验证，难免有 BUG，无能力阅读修改源代码的请暂时慎重选择。等待我们实战项目开发并完善稳定后再使用！

同时欢迎有志之士加入我们，一起开发完善！

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
- [x] Redis 连接池
- [x] 协程 MySQL 连接池
- [ ] 协程 PostgreSQL 连接池
- [x] Db 连贯操作
- [ ] Model ORM
- [ ] Task 异步任务
- [ ] RPC 远程调用
- [ ] WebSocket 服务器相关……
- [ ] TCP 服务器相关……

## 文档

[完全开发手册](https://doc.imiphp.com)

QQ群：74401592 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://shang.qq.com/wpa/qunwpa?idkey=e2e6b49e9a648aae5285b3aba155d59107bb66fde02e229e078bd7359cac8ac3)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.0
- [Composer](https://getcomposer.org/)
- [Swoole](https://www.swoole.com/) >= 2.2.0 (必须启用协程，如使用 Redis 请开启)
- [Hiredis](https://github.com/redis/hiredis/releases) (需要在安装 Swoole 之前装)

## 版权信息

IMI 遵循 Apache2 开源协议发布，并提供免费使用。

## 鸣谢

感谢以下开源项目 (按字母顺序排列) 为 IMI 提供强力支持！

- [doctrine/annotations](https://github.com/doctrine/annotations) (PHP 注解处理类库)
- [PHP](https://php.net/) (没有 PHP 就没有 IMI)
- [swoft/swoole-ide-helper](https://github.com/swoft-cloud/swoole-ide-helper) (为 IDE 提供代码提示)
- [Swoole](https://www.swoole.com/) (没有 Swoole 就没有 IMI)

## 项目起源

在机缘巧合下，我偶然接触到了 Swoole 2.x 版本，在简单了解和demo调试后我认为，Swoole 可能是未来 PHP 微服务架构开发必不可少的扩展。

众所周知，PHP 是被其它语言看不起的宇宙第一编程语言，PHP 仅仅是一个脚本语言，仅仅是一个模版引擎，没有强类型规范开发，维护非常不便。

PHP 7 来了，强类型的支持加入，增强了 PHP 的可维护性并提升了性能，IMI 底层就使用了非常多的强类型进行开发和规范。

宇润我在 2013 年开发并发布了第一个框架 YurunPHP，一直维护使用至今，非常稳定，并且有文档。

我待过的公司有用过这个框架，我还是很幸运的，有机会在实战中不断改进完善框架。

PHP 进入 Swoole 时代，我本着学习 Swoole 并且尝试能否开发一个基于 Swoole 的框架的想法，接触了解到了 EasySwoole 和 Swoft。

喜闻乐见的是，我先参考了一下这两个框架的文档和用法，再简单看了一下源代码。我决定还是先从 Swoole 看起，实战是最可以锻炼人的。于是我走上了 IMI 开发的不归路……
