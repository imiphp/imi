<p align="center">
    <a href="https://www.imiphp.com" target="_blank">
        <img src="https://raw.githubusercontent.com/Yurunsoft/IMI/dev/res/logo.png" alt="imi" />
    </a>
</p>

[![Latest Version](https://img.shields.io/packagist/v/yurunsoft/imi.svg)](https://packagist.org/packages/yurunsoft/imi)
[![Travis](https://img.shields.io/travis/Yurunsoft/IMI.svg)](https://travis-ci.org/Yurunsoft/IMI)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.3.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI Doc](https://img.shields.io/badge/docs-passing-green.svg)](https://doc.imiphp.com)
[![Backers on Open Collective](https://opencollective.com/IMI/backers/badge.svg)](#backers) 
[![Sponsors on Open Collective](https://opencollective.com/IMI/sponsors/badge.svg)](#sponsors) 
[![IMI License](https://img.shields.io/github/license/Yurunsoft/imi.svg)](https://github.com/Yurunsoft/imi/blob/master/LICENSE)

## 介绍

imi 是基于 PHP 协程应用开发框架，它支持 HttpApi、WebSocket、TCP、UDP 应用开发。

由 Swoole 提供强力驱动，Swoole 拥有常驻内存、协程非阻塞 IO 等特性。

框架遵守 PSR 标准规范，提供 AOP、注解、连接池、请求上下文管理、ORM模型等常用组件。

imi 的模型支持关联关系的定义，增删改查一把梭！

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

## 开始使用

[完全开发手册](https://doc.imiphp.com)

[新项目 Demo](https://gitee.com/yurunsoft/empty-imi-demo)

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题，负责的宇润全程手把手解决。

运行测试脚本：`composer test`

## 运行环境

- Linux 系统 (Swoole 不支持在 Windows 上运行)
- [PHP](https://php.net/) >= 7.1
- [Composer](https://getcomposer.org/)
- [Swoole](https://www.swoole.com/) >= 4.3.0
- Redis、PDO 扩展

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
* 文档编写（<https://github.com/Yurunsoft/imidoc>）
* 教程、博客分享

> 最新代码以 `dev` 分支为准，提交 `PR` 也请合并至 `dev` 分支！

## Contributors

This project exists thanks to all the people who contribute. 
<a href="https://github.com/Yurunsoft/IMI/graphs/contributors"><img src="https://opencollective.com/IMI/contributors.svg?width=890&button=false" /></a>

## Backers

Thank you to all our backers! 🙏 [[Become a backer](https://opencollective.com/IMI#backer)]

<a href="https://opencollective.com/IMI#backers" target="_blank"><img src="https://opencollective.com/IMI/backers.svg?width=890"></a>

## Sponsors

Support this project by becoming a sponsor. Your logo will show up here with a link to your website. [[Become a sponsor](https://opencollective.com/IMI#sponsor)]

<a href="https://opencollective.com/IMI/sponsor/0/website" target="_blank"><img src="https://opencollective.com/IMI/sponsor/0/avatar.svg"></a>
<a href="https://opencollective.com/IMI/sponsor/1/website" target="_blank"><img src="https://opencollective.com/IMI/sponsor/1/avatar.svg"></a>
<a href="https://opencollective.com/IMI/sponsor/2/website" target="_blank"><img src="https://opencollective.com/IMI/sponsor/2/avatar.svg"></a>
<a href="https://opencollective.com/IMI/sponsor/3/website" target="_blank"><img src="https://opencollective.com/IMI/sponsor/3/avatar.svg"></a>
<a href="https://opencollective.com/IMI/sponsor/4/website" target="_blank"><img src="https://opencollective.com/IMI/sponsor/4/avatar.svg"></a>
<a href="https://opencollective.com/IMI/sponsor/5/website" target="_blank"><img src="https://opencollective.com/IMI/sponsor/5/avatar.svg"></a>
<a href="https://opencollective.com/IMI/sponsor/6/website" target="_blank"><img src="https://opencollective.com/IMI/sponsor/6/avatar.svg"></a>
<a href="https://opencollective.com/IMI/sponsor/7/website" target="_blank"><img src="https://opencollective.com/IMI/sponsor/7/avatar.svg"></a>
<a href="https://opencollective.com/IMI/sponsor/8/website" target="_blank"><img src="https://opencollective.com/IMI/sponsor/8/avatar.svg"></a>
<a href="https://opencollective.com/IMI/sponsor/9/website" target="_blank"><img src="https://opencollective.com/IMI/sponsor/9/avatar.svg"></a>

## 捐赠

<img src="https://raw.githubusercontent.com/Yurunsoft/IMI/dev/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
