# 介绍

运行命令行工具有两种方式：

## 方式一

框架自带文件：`vendor/bin/imi-cli`

用框架自带执行命令行，需要手动传入`--app-namesapce "项目命名空间"`参数

## 方式二

项目自建文件，以imi-demo为例：`HttpDemo/bin/imi`

使用项目自建文件可以传入`--app-namesapce "项目命名空间"`参数，也可以通过配置文件指定。

`项目路径/config/config.php`中`namespace`设置为项目命名空间即可。
