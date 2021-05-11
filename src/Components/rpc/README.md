# imi-rpc

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-rpc.svg)](https://packagist.org/packages/imiphp/imi-rpc)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.1.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-rpc.svg)](https://github.com/imiphp/imi-rpc/blob/master/LICENSE)

## 介绍

在 imi 框架中开发 RPC 服务的基础组件。本组件不提供实际的 RPC 实现，仅提供开发 RPC 服务的一些插槽。

`imi-hprose` 基于 `imi-rpc` 实现：https://github.com/imiphp/imi-hprose

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-rpc": "~1.0"
    }
}
```

然后执行 `composer update` 安装。

## 使用

在项目 `config/config.php` 中配置：

```php
[
    'components'    =>  [
        // 引入本组件
        'rpc'    =>  'Imi\Rpc',
    ],
]
```

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.1
- [Composer](https://getcomposer.org/)
- [Swoole](https://www.swoole.com/) >= 4.1.0

## 版权信息

`imi-rpc` 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://raw.githubusercontent.com/imiphp/imi-rpc/dev/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
