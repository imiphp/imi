# imi - 基于 Swoole 的 PHP 协程开发框架

<p align="center">
    <a href="https://www.imiphp.com" target="_blank">
        <img src="https://raw.githubusercontent.com/Yurunsoft/IMI/dev/res/logo.png" alt="imi" />
    </a>
</p>

[![Latest Version](https://img.shields.io/packagist/v/yurunsoft/imi.svg)](https://packagist.org/packages/yurunsoft/imi)
![Cirrus CI - Base Branch Build Status](https://img.shields.io/cirrus/github/Yurunsoft/imi)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.3.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![imi Doc](https://img.shields.io/badge/docs-passing-green.svg)](https://doc.imiphp.com)
[![imi License](https://img.shields.io/badge/license-MulanPSL%201.0-brightgreen.svg)](https://github.com/Yurunsoft/imi/blob/master/LICENSE)
[![star](https://gitee.com/yurunsoft/IMI/badge/star.svg?theme=gvp)](https://gitee.com/yurunsoft/IMI/stargazers)

## 介绍

imi 是基于 PHP Swoole 的高性能协程应用开发框架，它支持 HttpApi、WebSocket、TCP、UDP、MQTT 服务的开发。

在 Swoole 的加持下，相比 php-fpm 请求响应能力，I/O密集型场景处理能力，有着本质上的提升。

imi 框架拥有丰富的功能组件，可以广泛应用于互联网、移动通信、企业软件、云计算、网络游戏、物联网（IOT）、车联网、智能家居等领域。可以使企业 IT 研发团队的效率大大提升，更加专注于开发创新产品。

> 目前 imi v2 版本已经开始开发了（2020-09），v1 版本进入维护期，仅修复问题不再加入新特性。如果有任何问题，欢迎联系我们！

imi 框架交流群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)

## 官方视频教程（完全免费）

imi 框架入门教程（11集全）<https://www.bilibili.com/video/av78158909>

imi 框架进阶教程（五子棋服务端开发，每周连载中）<https://space.bilibili.com/768718/channel/detail?cid=136926>

### 核心组件

* HttpApi、WebSocket、TCP、UDP、MQTT 服务器
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

### 扩展组件

* [MQTT](../../../../imiphp/imi-mqtt)
* [RPC](../../../../imiphp/imi-rpc)
* [gRPC](../../../../imiphp/imi-grpc)
* [Hprose](../../../../imiphp/imi-hprose)
* [AMQP](../../../../imiphp/imi-amqp) (支持 AMQP 协议的消息队列都可用，如：RabbitMQ)
* [JWT](../../../../imiphp/imi-jwt) (在 imi 框架中非常方便地接入 jwt)
* [权限控制](../../../../imiphp/imi-access-control)
* [Smarty 模版引擎](../../../../imiphp/imi-smarty)
* [限流](../../../../imiphp/imi-rate-limit)
* [跨进程变量共享](../../../../imiphp/imi-shared-memory)
* [Swoole Tracker](../../../../imiphp/imi-swoole-tracker)
* [雪花算法发号器](../../../../imiphp/imi-snowflake)
* [Swagger API 文档生成](../../../../imiphp/imi-apidoc)

## 开始使用

创建 Http Server 项目：`composer create-project imiphp/project-http`

创建 WebSocket Server 项目：`composer create-project imiphp/project-websocket`

创建 TCP Server 项目：`composer create-project imiphp/project-tcp`

创建 UDP Server 项目：`composer create-project imiphp/project-udp`

[完全开发手册](https://doc.imiphp.com)

## 运行环境

* Linux 系统 (Swoole 不支持在 Windows 上运行)
* [PHP](https://php.net/) >= 7.1
* [Composer](https://getcomposer.org/)
* [Swoole](https://www.swoole.com/) >= 4.3.0
* Redis、PDO 扩展

## Docker

推荐使用 Swoole 官方 Docker：<https://github.com/swoole/docker-swoole>

## 成功案例

不论您使用 imi 开发的是个人项目还是公司项目，不管是开源还是商业，都可以向我们提交案例。

案例可能会被采纳并展示在 imi 官网、Swoole 官网等处，这对项目的推广和发展有着促进作用。

**提交格式：**

* 项目名称
* 项目介绍
* 项目地址（官网/下载地址/Github等至少一项）
* 联系方式（电话/邮箱/QQ/微信等至少一项）
* 项目截图（可选）
* 感言

### 案例展示

* [教书先生API - 提供免费接口调用平台](https://api.oioweb.cn/)

![教书先生API](https://www.imiphp.com/images/anli/jsxsapi.png "教书先生API")

**项目介绍：** 教书先生API是免费提供API数据接口调用服务平台 - 我们致力于为用户提供稳定、快速的免费API数据接口服务。

**感言：**

之前的话服务器配置是8H8G 30M这样的一个配置，每天日300万+的一个请求量，有一次是某个接口因一个错误时不时会导致服务器直接宕机，一个偶然的搜索看到了群主（宇润）大佬的一个IMI项目，于是熬夜给程序内部请求核心代码换上了IMI，正好手里面有一台1H2G 5M的服务器，拿来测试了一下，配合Redis 200万-300万+一点问题都没有的，最后还是要感谢宇润大佬的开源项目。

---

## 版权信息

imi 遵循 木兰宽松许可证(Mulan PSL v2) 开源协议发布，并提供免费使用。

## 鸣谢

感谢以下开源项目 (按字母顺序排列) 为 imi 提供强力支持！

* [doctrine/annotations](https://github.com/doctrine/annotations) (PHP 注解处理类库)
* [PHP](https://php.net/) (没有 PHP 就没有 imi)
* [Swoole](https://www.swoole.com/) (没有 Swoole 就没有 imi)

## 贡献者

[![贡献者](https://opencollective.com/IMI/contributors.svg?width=890&button=false)](https://github.com/Yurunsoft/IMI/graphs/contributors)

你想出现在贡献者列表中吗？

你可以做的事（包括但不限于以下）：

* 纠正拼写、错别字
* 完善注释
* bug修复
* 功能开发
* 文档编写（<https://github.com/Yurunsoft/imidoc>）
* 教程、博客分享

> 最新代码以 `dev` 分支为准，提交 `PR` 也请合并至 `dev` 分支！

提交 `Pull Request` 到本仓库，你就有机会成为 imi 的作者之一！

## 关于测试用例

### 环境要求

Redis、MySQL

### 首次运行测试

* 创建 `db_imi_test` 数据库，将 `tests/db/db.sql` 导入到数据库

* 配置系统环境变量，如果默认值跟你的一样就无需配置了

名称 | 描述 | 默认值
-|-|-
SERVER_HOST | 测试用的服务，监听的主机名 | 127.0.0.1 |
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

## 捐赠

![捐赠](https://cdn.jsdelivr.net/gh/Yurunsoft/IMI@dev/res/pay.png)

开源不求盈利，多少都是心意，生活不易，随缘随缘……
