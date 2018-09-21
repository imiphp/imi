<p align="center">
    <a href="https://www.imiphp.com" target="_blank">
        <img src="https://raw.githubusercontent.com/Yurunsoft/IMI/dev/logo.png" alt="imi" />
    </a>
</p>

[![Latest Version](https://img.shields.io/packagist/v/yurunsoft/imi.svg)](https://packagist.org/packages/yurunsoft/imi)
[![Travis](https://img.shields.io/travis/Yurunsoft/IMI.svg)](https://travis-ci.org/Yurunsoft/IMI)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.0.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![Hiredis Version](https://img.shields.io/badge/hiredis-%3E=0.1-brightgreen.svg)](https://github.com/redis/hiredis)
[![IMI Doc](https://img.shields.io/badge/docs-passing-green.svg)](https://doc.imiphp.com)
[![IMI License](https://img.shields.io/github/license/Yurunsoft/imi.svg)](https://github.com/Yurunsoft/imi/blob/master/LICENSE)

## 介绍

IMI 是基于 Swoole 开发的协程 PHP 开发框架，完美支持 Http、WebSocket、TCP、UDP 开发，拥有常驻内存、协程异步非阻塞IO等优点。

IMI 框架文档丰富，上手容易，致力于让开发者跟使用传统 MVC 框架一样顺手。

IMI 框架底层开发使用了强类型，易维护，性能更强。支持 Aop ，支持使用注解和配置文件注入，完全遵守 PSR-3、4、7、11、15、16 标准规范。

框架的扩展性强，开发者可以根据实际需求，自行开发相关驱动进行扩展。不止于框架本身提供的功能和组件！

> 框架暂未实战验证，请无能力阅读和修改源代码的开发者，暂时不要用于实际项目开发，等待我们的实战检验完善，我们不希望因此为您造成不便！

### 功能组件

- [x] Aop (注解 / 配置文件)
- [x] Container (PSR-11)
- [x] 注解
- [x] 全局事件/类事件
- [x] HttpServer
- [x] HttpRequest/HttpResponse (PSR-7)
- [x] Http 中间件、注解路由、配置文件路由 (PSR-15)
- [x] Session (File + Redis)
- [x] View (html + json + xml)
- [x] 日志 (PSR-3 / File + Console)
- [x] 缓存 (PSR-16 / File + Redis)
- [x] Redis 连接池
- [x] 协程 MySQL 连接池
- [x] PDO 连接池
- [ ] 协程 PostgreSQL 连接池
- [x] Db 连贯操作
- [x] 关系型数据库 模型 ORM
- [x] 跨进程共享内存表 模型 ORM
- [x] Redis 模型 ORM
- [x] Task 异步任务
- [x] 命令行开发辅助工具
- [ ] 图形化管理工具
- [x] 业务代码热更新
- [ ] RPC 远程调用
- [x] WebSocket 服务开发
- [x] TCP 服务开发
- [x] UDP 服务开发

> 日志、缓存都支持：多驱动 + 多实例 + 统一操作入口
> 
> 所有连接池都支持：同步 + 异步 + 多驱动 + 多实例

## 文档

[完全开发手册](https://doc.imiphp.com)

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.1
- [Composer](https://getcomposer.org/)
- [Swoole](https://www.swoole.com/) >= 4.0.0 (必须启用协程，如使用 Redis 请开启)
- [Hiredis](https://github.com/redis/hiredis/releases) (需要在安装 Swoole 之前装)

## 版权信息

IMI 遵循 Apache2 开源协议发布，并提供免费使用。

## 鸣谢

感谢以下开源项目 (按字母顺序排列) 为 IMI 提供强力支持！

- [doctrine/annotations](https://github.com/doctrine/annotations) (PHP 注解处理类库)
- [PHP](https://php.net/) (没有 PHP 就没有 IMI)
- [swoft/swoole-ide-helper](https://github.com/swoft-cloud/swoole-ide-helper) (为 IDE 提供代码提示)
- [Swoole](https://www.swoole.com/) (没有 Swoole 就没有 IMI)

## 寻找有缘人

想要加入我们吗？开源项目不能只靠一两个人，而是要靠大家的努力来完善~

我们需要你的加入，你可以做的事（包括但不限于以下）：

* 纠正拼写、错别字
* 完善注释
* bug修复
* 功能开发
* 文档编写（https://github.com/Yurunsoft/imidoc）
* 教程、博客分享

> 最新代码以 `dev` 分支为准，提交 `PR` 也请合并至 `dev` 分支！

## 项目起源

在机缘巧合下，我偶然接触到了 Swoole 2.x 版本，在简单了解和demo调试后我认为，Swoole 可能是未来 PHP 微服务架构开发必不可少的扩展。

众所周知，PHP 是被其它语言看不起的宇宙第一编程语言，PHP 仅仅是一个脚本语言，仅仅是一个模版引擎，没有强类型规范开发，维护非常不便。

PHP 7 来了，强类型的支持加入，增强了 PHP 的可维护性并提升了性能，IMI 底层就使用了非常多的强类型进行开发和规范。

宇润我在 2013 年开发并发布了第一个框架 YurunPHP，一直维护使用至今，非常稳定，并且有文档。

我待过的公司有用过这个框架，我还是很幸运的，有机会在实战中不断改进完善框架。

PHP 进入 Swoole 时代，我本着学习 Swoole 并且尝试能否开发一个基于 Swoole 的框架的想法，接触了解到了 EasySwoole 和 Swoft。

喜闻乐见的是，我先参考了一下这两个框架的文档和用法，再简单看了一下源代码。我决定还是先从 Swoole 看起，实战是最可以锻炼人的。于是我走上了 IMI 开发的不归路……
