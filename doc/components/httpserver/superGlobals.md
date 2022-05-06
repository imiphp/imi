# 超全局变量

[toc]

一般来讲，在非 PHP-FPM 项目中，你是无法使用 `$_GET`、`$_POST`等超全局变量的。

自 imi v1.0.15 版本开始支持，启用方法：

在项目配置文件的 `beans` 中加入：

```php
[
    'SuperGlobals'  =>  [
        'enable'    =>  true,
    ],
]
```
