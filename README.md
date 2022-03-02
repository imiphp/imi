# imi - PHP 长连接微服务分布式开发框架

<p align="center">
    <a href="https://www.imiphp.com" target="_blank">
        <img src="https://cdn.jsdelivr.net/gh/imiphp/imi@2.1/res/logo.png" alt="imi" />
    </a>
</p>

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi.svg)](https://packagist.org/packages/imiphp/imi)
![GitHub Workflow Status (branch)](https://img.shields.io/github/workflow/status/imiphp/imi/ci/dev)
[![Php Version](https://img.shields.io/badge/php-%3E=7.4-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.8.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![imi Doc](https://img.shields.io/badge/docs-passing-green.svg)](https://doc.imiphp.com/v2.1/)
[![imi License](https://img.shields.io/badge/license-MulanPSL%201.0-brightgreen.svg)](https://github.com/imiphp/imi/blob/master/LICENSE)
[![star](https://gitee.com/yurunsoft/IMI/badge/star.svg?theme=gvp)](https://gitee.com/yurunsoft/IMI/stargazers)

## 介绍

imi 是一款支持长连接微服务分布式的 PHP 开发框架，它可以运行在 PHP-FPM、Swoole、Workerman、RoadRunner 等多种容器环境下。

imi 拥有丰富的功能组件，v2.1 版本内置了 2 个分布式长连接服务的解决方案。

imi 框架现在已经稳定运行在：文旅电商平台、物联网充电云平台、停车云平台、支付微服务、短信微服务、钱包微服务、卡牌游戏服务端、数据迁移服务（虎扑）等项目中。

> imi 第一个版本发布于 2018 年 6 月 21 日

imi 框架交流群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)

## 官方视频教程（完全免费）

imi 2.0 基础视频教程(免费连载中):

<https://space.bilibili.com/768718/channel/seriesdetail?sid=274078>
<https://www.zhihu.com/people/yurunsoft/zvideos>

imi 1.0 框架入门教程（免费11集全）<https://www.bilibili.com/video/av78158909>

imi 框架进阶教程——五子棋游戏开发(免费7集全)<https://space.bilibili.com/768718/channel/detail?cid=136926>

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

### 扩展组件

#### 官方组件

* [PostgreSQL](doc/components/db/pgsql.md)
* [MQTT](src/Components/mqtt)
* [RPC](src/Components/rpc)
* [gRPC](src/Components/grpc)
* [Hprose](src/Components/hprose)
* [消息队列](src/Components/queue)
* [AMQP](src/Components/amqp) (支持 AMQP 协议的消息队列都可用，如：RabbitMQ)
* [Kafka](src/Components/kafka)
* [JWT](src/Components/jwt) (在 imi 框架中非常方便地接入 jwt)
* [权限控制](src/Components/access-control)
* [Smarty 模版引擎](src/Components/smarty)
* [限流](src/Components/rate-limit)
* [跨进程变量共享](src/Components/shared-memory)
* [雪花算法发号器](src/Components/snowflake)
* [Swagger API 文档生成](src/Components/apidoc)
* [Swoole Tracker](src/Components/swoole-tracker)

> 这些组件都已经在 imi 主仓库中维护

#### 优秀的第三方组件

* [权限控制 (phpben/imi-auth)](https://gitee.com/phpben/imi-auth)
* [注册中心 (phpben/imi-config-center)](https://gitee.com/phpben/imi-config-center)
* [模块化路由 (phpben/imi-module-route)](https://gitee.com/phpben/imi-module-route)
* [ThinkPHP6 验证器 (phpben/imi-validate)](https://gitee.com/phpben/imi-validate)

#### 优秀的开源项目

* [后台管理框架 (phpben/imi-admin)](https://gitee.com/phpben/imi-admin)

## 开始使用

创建 Http Server 项目：`composer create-project imiphp/project-http:~2.1.0`

创建 WebSocket Server 项目：`composer create-project imiphp/project-websocket:~2.1.0`

创建 TCP Server 项目：`composer create-project imiphp/project-tcp:~2.1.0`

创建 UDP Server 项目：`composer create-project imiphp/project-udp:~2.1.0`

创建 MQTT Server 项目：`composer create-project imiphp/project-mqtt:~2.1.0`

[完全开发手册](https://doc.imiphp.com/v2.1/)

## 运行环境

* Linux 系统 (Swoole 不支持在 Windows 上运行)
* [PHP](https://php.net/) >= 7.4
* [Composer](https://getcomposer.org/) >= 2.0
* [Swoole](https://www.swoole.com/) >= 4.8.0
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

成功案例：<https://www.imiphp.com/case.html>

> imi 有你的案例会发展得更好，欢迎有条件的用户将项目案例挂上来，同时也是一种免费的宣传！

---

## 版权信息

imi 遵循 木兰宽松许可证(Mulan PSL v2) 开源协议发布，并提供免费使用。

## 鸣谢

感谢以下开源项目 (按字母顺序排列) 为 imi 提供强力支持！

* [doctrine/annotations](https://github.com/doctrine/annotations) (PHP 注解处理类库)
* [PHP](https://php.net/) (没有 PHP 就没有 imi)
* [Swoole](https://www.swoole.com/) (没有 Swoole 就没有 imi)

## 贡献者

[![贡献者](https://opencollective.com/IMI/contributors.svg?width=890&button=false)](https://github.com/imiphp/imi/graphs/contributors)

你想出现在贡献者列表中吗？

你可以做的事（包括但不限于以下）：

* 纠正拼写、错别字
* 完善注释
* bug修复
* 功能开发
* 文档编写
* 教程、博客分享

> 最新代码以 `dev` 分支为准，提交 `PR` 也请合并至 `dev` 分支！

提交 `Pull Request` 到本仓库，你就有机会成为 imi 的作者之一！

参与框架开发教程详见：<doc/adv/devp.md>

## 捐赠

![捐赠](https://cdn.jsdelivr.net/gh/imiphp/imi@2.1/res/pay.png)

开源不求盈利，多少都是心意，生活不易，随缘随缘……
