# Swoole 环境安装教程

[toc]

## 运行环境

- Linux 系统 (Swoole 不支持在 Windows 上运行)
- [PHP](https://php.net/) >= 7.4
- [Composer](https://getcomposer.org/) >= 2.0
- [Swoole](https://www.swoole.com/) >= 4.8.0
- Redis、PDO 扩展

## Windows 开发者

可以使用 Windows 10 Linux 子系统、Docker 或虚拟机等环境，实现在 Windows 系统上开发和调试。

## swoole-cli

### swoole-cli 介绍

`Swoole-Cli` 是一个 `PHP` 的二进制发行版，集成了 `swoole`、`php 内核`、`php-cli`、`php-fpm` 以及多个常用扩展。`Swoole-Cli`
是全部静态编译打包的，不依赖任何操作系统的 `so` 动态链接库，具备非常好的移植性，可以在任意 `Linux`/`macOS`/`Windows(CygWin)`
系统之间复制，下载即可使用。

下载地址：<https://github.com/swoole/swoole-src/releases>

开源地址：<https://github.com/swoole/swoole-cli>

> 需注意 CygWin 版，无法用于 imi 框架

### 单文件打包

imi 作者宇润为 swoole-cli 贡献了[实现打包 PHP 文件进二进制可执行文件](https://github.com/swoole/swoole-cli/pull/55)，从 `v5.0.3` 版本开始可以实现打包 PHP 文件进二进制可执行文件，也可以整项目可以打包为 phar 后再打包进可执行文件，支持 phar 的压缩。

**打包：**`./swoole-cli ./pack-sfx.php 你的php文件名 目标可执行文件`

**运行打包进可执行文件的代码：**`目标可执行文件 --self`

**运行其它文件：**`目标可执行文件 test.php`

## Docker

推荐使用 Swoole 官方 Docker：<https://github.com/swoole/docker-swoole>

> 支持 Windows

## 安装教程

> 教程可能过期，仅供参考！

### 虚拟机安装 Linux

- [【宇润】VirtualBox 虚拟机安装 Ubuntu 16.04](https://www.bilibili.com/video/av88488788)

- [【宇润】VirtualBox 虚拟机安装 Ubuntu 18.04](https://www.bilibili.com/video/av88712228)

- [【宇润】VirtualBox 虚拟机安装 CentOS 7](https://www.bilibili.com/video/av89707677)

- [【宇润】VirtualBox 虚拟机安装 CentOS 8.1](https://www.bilibili.com/video/av89935801)

### PHP 环境安装

- [【宇润】Ubuntu、Debian 系统安装 php 一把梭教程](https://www.bilibili.com/video/av89346440)

- [【宇润】CentOS 系统安装 php 一把梭教程（RedHat Linux + yum）](https://www.bilibili.com/video/av89346440)

### Swoole 环境安装

- [【宇润】一键安装 Swoole 环境教程，一把梭，让天下没有难装的 Swoole](https://www.bilibili.com/video/av90802466)
