# Smarty 模版引擎

## 介绍

支持在 imi 框架中使用 Smarty 模版引擎

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-smarty": "2.0.x-dev"
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
            'caching'       =>  0,
            // 缓存有效时间
            'cacheLifetime' =>  0,
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
