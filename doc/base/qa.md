# 常见问题

[toc]

## 通过 `composer create-project` 创建项目报错问题

**解决方案：** 请确保你的环境依赖版本符合要求：PHP >= 7.4 && Composer >= 2.0

查看命令：`php -v && composer -V`

## 通过 `composer create-project` 创建项目后无法以 Swoole 模式运行

**解决方案：** 为 Windows 系统用户兼容考虑，默认没有引入 Swoole 组件，如有需要请手动引入：`composer require imiphp/imi-swoole`

## Composer 引入 `imi-swoole` 组件报错

**解决方案：** 请确保你的 Swoole >= 4.8.0

查看命令：`php --ri swoole`

## 更新框架后运行报错

**解决方案：** 尝试删除 `.runtime` 目录中的 `imi-runtime` 和 `runtime` 目录

你也可以使用命令来删除：`vendor/bin/imi-xxx imi/clearRuntime && vendor/bin/imi-xxx imi/clearImiRuntime` (`xxx` 根据运行模式不同而不同)

## PHP Warning:  exec() has been disabled for security reasons

**解决方案：** 不要禁用 `exec、shell_exec`，在 `php.ini` 中修改 `disable_functions` 项

## imi 框架的组件能不能用于其他框架中

目前暂时是不能的

## `Imi\` 命名空间下的类报错提示不存在

当项目文件放置在，共享目录等不支持文件锁的文件系统时，可以配置一个支持文件锁的目录。比如：`/tmp`。

* 可以在运行命令时指定环境变量：`IMI_MACRO_LOCK_FILE_DIR=/tmp vendor/bin/imi-swoole swoole/start`

## Can't create more than max_prepared_stmt_count statements

```log
PDOException: SQLSTATE[42000]: Syntax error or access violation: 1461 Can't create more than max_prepared_stmt_count statements (current value: 16382)
```

imi 默认会缓存 Statement，建议调大 mysql 的 `max_prepared_stmt_count` 配置值。

从 imi v2.1.34 开始你也可以配置 [Statement 最大缓存数量](https://doc.imiphp.com/v2.1/components/db/config.html) 来自动清理缓存的 Statement。

## Workerman 报错：send buffer full and drop package

这个问题一般出现在，服务器循环发送数据给客户端，导致塞满发送缓冲区。

如果你是 Workerman，可以在入口文件中加上：

```php
// 发送缓冲区大小，单位：字节。可以根据需要调整，不建议设置得很大，会浪费内存
\Workerman\Connection\TcpConnection::$defaultMaxSendBufferSize = 2 * 1024 * 1024;
```

如果你是 Workerman Gateway，可以在服务器配置中的 `configs` 加上相应配置：

```php
[
    'gateway' => [
        'namespace'   => 'Imi\WorkermanGateway\Test\AppServer\Gateway',
        'type'        => Imi\WorkermanGateway\Workerman\Server\Type::GATEWAY,
        'socketName'  => 'websocket://0.0.0.0:8081', // 网关监听的地址
        'configs'     => [
            'lanIp'           => '127.0.0.1',
            'startPort'       => 12900,
            'registerAddress' => '127.0.0.1:13004',
            // 发送缓冲区大小，单位：字节。可以根据需要调整，不建议设置得很大，会浪费内存
            'sendToClientBufferSize' => 2 * 1024 * 1024,
        ],
    ],
]
```

## Workerman Gateway 网关模式发送大数据失败

修改：`/etc/sysctl.conf`

```conf
#TCP接收/发送缓存的最小值、默认值、最大值
net.ipv4.tcp_rmem = 4096 32768 262142
net.ipv4.tcp_wmem = 4096 32768 262142
```

> 将最大值调整为你希望达到的大小

保存后：`sysctl -p`
