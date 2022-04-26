# 参与框架开发

## 仓库

Github：https://github.com/imiphp/imi

## 目录结构

```text
├── bin         // 可执行文件路径
├── config      // 框架核心配置文件
├── doc         // 文档 markdown 源文件
├── mddoc       // 文档 html 模版
├── res         // 资源文件
├── src         // PHP 源代码
├── tests       // 测试用例目录
└── website     // 官网 Html 源代码
```

## 基本操作

* 在 Github 的 imi 页面点击 `fork` 按钮，将项目复刻到你自己名下。

* 将代码拉到本地，在最新的 `dev` 分支基础上创建一个用于此次贡献的分支。

* 此次贡献的代码都提交到上一步创建的分支中。

* 在 Github 的 imi 页面点击 `Pull requests`，然后点击 `New pull request` 把你修改的分支申请合并。

## 参与方式

### 文档

imi 的文档是用 markdown 编写，使用 mddoc 将 markdown 转换为 html 用于访问。

文档源代码在 imi 仓库中的 `doc` 目录，mddoc 的 html 模版在 `mddoc` 目录中。

你可以帮助完善文档，包括但不限于修正错别字、补充文档说明、贡献示例代码等。

### 代码

imi 完全使用 PHP 开发，一个稍微有点能力的 PHP 开发者，应该都可以参与进来。

你可以帮助 imi 变得更好更强大，包括但不限于修复 BUG、贡献新功能等。

贡献代码需要能跑通原有测试用例，并且为新增功能特性编写测试用例。

#### 关于测试用例

##### 环境要求

Redis、MySQL

##### 首次运行测试

* 执行 `tests/db/install-db.sh` 导入数据库表结构

* 配置系统环境变量，如果默认值跟你的一样就无需配置了

名称 | 描述 | 默认值
-|-|-
SERVER_HOST | 测试用的服务，监听的主机名 | 127.0.0.1
MYSQL_SERVER_HOST | MySQL 主机名 | 127.0.0.1
MYSQL_SERVER_PORT | MySQL 端口 | 3306
MYSQL_SERVER_USERNAME | MySQL 用户名 | root
MYSQL_SERVER_PASSWORD | MySQL 密码 | root
REDIS_SERVER_HOST | Redis 主机名 | 127.0.0.1
REDIS_SERVER_PORT | Redis 端口 | 6379
REDIS_SERVER_PASSWORD | Redis 密码 |
REDIS_CACHE_DB | Redis 缓存用的 `db`，该 `db` 会被清空数据，请慎重设置 | 1

配置命令：`export NAME=VALUE`

* 首次运行测试脚本：`composer install-test`

* 首次之后再运行测试的命令：`composer test`

### 官方网站

imi 官方网站 (<https://www.imiphp.com>) 的源代码在 imi 仓库中的 `website` 目录中。

你可以帮助完善官网说明，如果你有更好设计想法，也可以与我们沟通或者直接贡献代码。
