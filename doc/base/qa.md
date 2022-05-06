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
