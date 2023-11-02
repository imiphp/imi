# 数据库迁移

[TOC]

长期以来，数据库迁移一直是个麻烦事。当我们部署代码到服务器上时，希望可以轻松简单地升级我们的表结构。可能会有人用 Navicat 的结构同步来升级，这个确实好用，但有商业风险。

常见的建表方式有：使用图形化工具建表、使用 PHP 代码定义表结构、手写 SQL 建表等。

imi 的数据库迁移依赖模型功能，支持：使用图形化工具、手写 SQL 建表，只要你最终生成了模型，就可以使用数据库迁移功能。

当你使用 imi 模型生成工具生成模型时，会把创建表的 SQL 语句定义在 `DDL` 注解中。使用数据库迁移工具，可以自动比较模型定义与数据库服务器上的表（视图）结构差异，并且生成升级语句，升级语句不会造成表数据的丢失。

> 正在测试阶段，使用时请先确认 SQL 无误后再执行，本项目不对删库删数据负责。

项目地址：<https://github.com/imiphp/imi-migration>

## 版权信息

`imi-migration` 依赖 [phpmyadmin/sql-parser](https://github.com/phpmyadmin/sql-parser)，所以开源协议受到污染，必须是 GPL-2.0，所有基于本项目的代码都要开源。

建议仅将此组件作为独立工具安装使用，不要在项目中调用此项目中的任意代码，这样就不受开源协议污染了！

常用的 Linux 和 MySQL 都是 GPL 开源协议，只要使用得当是不会有法律风险的。

## 安装

`composer require imiphp/imi-migration:~3.0.0`

## 使用说明

### 模型同步到表结构

#### 同步表结构

将数据库中的数据表结构升级为模型中定义的结构。

```shell
vendor/bin/imi-swoole migration/patch -f
```

#### 生成同步结构 SQL 语句

**输出到命令行：**

```shell
vendor/bin/imi-swoole migration/patch
```

**保存到文件：**

```shell
vendor/bin/imi-swoole migration/patch -f "文件名"
```

### 数据库迁移功能

#### 配置

`@app.beans`:

```php
[
    \Imi\Migration\Service\MigrationService::class => [
        'handler' => \Imi\Migration\Handler\FileMigrationHandler::class, // 迁移处理器
        'onGenerateModel' => true, // 是否在生成模型时自动生成迁移文件
    ],
]
```

> 上述配置是默认配置，不配置时自动启用。

#### 目录

`.migration` 是存放数据库迁移文件和版本信息的目录，请勿将 `.migration/version` 提交到版本控制系统。

#### 执行数据库迁移

**执行前询问：**

```shell
vendor/bin/imi-swoole migration/migrate
```

**强制执行：**

```shell
vendor/bin/imi-swoole migration/migrate -f
```

> 请谨慎操作

### 执行数据库回滚

**执行前询问：**

```shell
vendor/bin/imi-swoole migration/rollback
```

**强制执行：**

```shell
vendor/bin/imi-swoole migration/rollback -f
```

> 请谨慎操作

### 通用参数

#### 指定连接池

```shell
vendor/bin/imi-swoole migration/命令 --poolName "连接池名"
```

> 不指定时使用默认连接池

#### 指定连接参数

```shell
vendor/bin/imi-swoole migration/命令 --driver "PdoMysqlDriver" --options "host=127.0.0.1&port=3306&username=root&password=root"
```
