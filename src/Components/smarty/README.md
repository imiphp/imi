# imi-smarty

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-smarty.svg)](https://packagist.org/packages/imiphp/imi-smarty)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.4.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-smarty.svg)](https://github.com/imiphp/imi-smarty/blob/master/LICENSE)

## 介绍

支持在 imi 框架中使用 Smarty 模版引擎

> 本仓库仅用于浏览，不接受 issue 和 Pull Requests，请前往：<https://github.com/Yurunsoft/imi>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-smarty": "~1.0"
    }
}
```

然后执行 `composer update` 安装。

## 基本使用

在项目 `config/config.php` 中配置：

```php
[
    'components'    =>  [
        // 引入本组件
        'Smarty'       =>  'Imi\Smarty',
    ],
]
```

在服务器的 `config/config.php` 中配置：

```php
[
    'beans'    =>    [
        'HtmlView'    =>    [
            'templatePath'      =>  dirname(__DIR__) . '/template/',
            'templateEngine'    =>  'SmartyEngine',
            // 支持的模版文件扩展名，优先级按先后顺序
            'fileSuffixs'        =>    [
                'tpl',
                'html',
                'php'
            ],
        ],
        // 可选项
        'SmartyEngine' => [
            // 缓存目录
            'cacheDir'      =>  null,
            // 编译目录
            'compileDir'    =>  null,
            // 是否开启缓存，默认不开启
            'caching'       =>  null,
            // 缓存有效时间
            'cacheLifetime' =>  null,
        ],
    ],
];
```

## 进阶使用

本组件没有封装大量的配置项，所以当你需要做一些其它设置时，可以监听 `IMI.SMARTY.NEW` 事件，当首次实例化对象时，触发该事件。

事件参数数据如下：

```php
[
    'smarty'        =>  $smarty,        // Smarty 对象
    'serverName'    =>  $serverName,    // 当前服务器名
]
```

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.1
- [Composer](https://getcomposer.org/)
- [Swoole](https://www.swoole.com/) >= 4.4.0

## 版权信息

`imi-smarty` 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://cdn.jsdelivr.net/gh/Yurunsoft/IMI@dev/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
