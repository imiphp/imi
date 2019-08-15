<p align="center">
    <a href="https://www.imiphp.com" target="_blank">
        <img src="https://raw.githubusercontent.com/Yurunsoft/IMI/dev/res/logo.png" alt="imi" />
    </a>
</p>

[![Latest Version](https://img.shields.io/packagist/v/yurunsoft/imi.svg)](https://packagist.org/packages/yurunsoft/imi)
[![Travis](https://img.shields.io/travis/Yurunsoft/IMI.svg)](https://travis-ci.org/Yurunsoft/IMI)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.3.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![imi Doc](https://img.shields.io/badge/docs-passing-green.svg)](https://doc.imiphp.com)
[![Backers on Open Collective](https://opencollective.com/IMI/backers/badge.svg)](#backers) 
[![Sponsors on Open Collective](https://opencollective.com/IMI/sponsors/badge.svg)](#sponsors) 
[![imi License](https://img.shields.io/github/license/Yurunsoft/imi.svg)](https://github.com/Yurunsoft/imi/blob/master/LICENSE)

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

创建 Http Server 项目：`composer create-project imiphp/project-http`

创建 WebSocket Server 项目：`composer create-project imiphp/project-websocket`

创建 TCP Server 项目：`composer create-project imiphp/project-tcp`

创建 UDP Server 项目：`composer create-project imiphp/project-udp`

[完全开发手册](https://doc.imiphp.com)

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题，负责的宇润全程手把手解决。

## 关于测试脚本

### 环境要求

Redis、MySQL

### 首次运行测试

* 创建 `db_imi_test` 数据库，将 `tests/db/db.sql` 导入到数据库

* 配置系统环境变量，如果默认值跟你的一样就无需配置了

名称 | 描述 | 默认值
-|-|-
MYSQL_SERVER_HOST | MySQL 主机名 | 127.0.0.1 |
MYSQL_SERVER_PORT | MySQL 端口 | 3306 |
MYSQL_SERVER_USERNAME | MySQL 用户名 | root |
MYSQL_SERVER_PASSWORD | MySQL 密码 | root |
REDIS_SERVER_HOST | Redis 主机名 | 127.0.0.1 |
REDIS_SERVER_PORT | Redis 端口 | 6379 |
REDIS_SERVER_PASSWORD | Redis 密码 |  |
REDIS_CACHE_DB | Redis 缓存用的 `db`，该 `db` 会被清空数据，请慎重设置 | 1 |

配置命令：`export NAME=VALUE`

* 首次运行测试脚本：`composer install-test`

* 首次之后再运行测试的命令：`composer test`

## 运行环境

- Linux 系统 (Swoole 不支持在 Windows 上运行)
- [PHP](https://php.net/) >= 7.1
- [Composer](https://getcomposer.org/)
- [Swoole](https://www.swoole.com/) >= 4.3.0
- Redis、PDO 扩展

## 版权信息

imi 遵循 木兰宽松许可证(Mulan PSL v1) 开源协议发布，并提供免费使用。

## 鸣谢

感谢以下开源项目 (按字母顺序排列) 为 imi 提供强力支持！

- [doctrine/annotations](https://github.com/doctrine/annotations) (PHP 注解处理类库)
- [PHP](https://php.net/) (没有 PHP 就没有 imi)
- [swoft/swoole-ide-helper](https://github.com/swoft-cloud/swoole-ide-helper) (为 IDE 提供代码提示)
- [Swoole](https://www.swoole.com/) (没有 Swoole 就没有 imi)

## Contributors

This project exists thanks to all the people who contribute. 
<a href="https://github.com/Yurunsoft/IMI/graphs/contributors"><img src="https://opencollective.com/IMI/contributors.svg?width=890&button=false" /></a>

你想出现在上图中吗？

你可以做的事（包括但不限于以下）：

* 纠正拼写、错别字
* 完善注释
* bug修复
* 功能开发
* 文档编写（<https://github.com/Yurunsoft/imidoc>）
* 教程、博客分享

> 最新代码以 `dev` 分支为准，提交 `PR` 也请合并至 `dev` 分支！

提交 `Pull Request` 到本仓库，你就有机会成为 imi 的作者之一！

## 捐赠

<img src="https://raw.githubusercontent.com/Yurunsoft/IMI/dev/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
