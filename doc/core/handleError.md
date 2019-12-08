# 错误转为异常捕获

imi 框架底层支持将错误转为异常，可以通过 `try...catch` 来捕获。

默认是不启用的，你可以通过配置来设定错误捕获的等级，并且启用它：

在 `config.php` 中的 `beans` 配置

```php
[
    'ErrorLog'  =>  [
        'level' =>  E_ERROR | E_WARNING | E_PARSE, // 报告 runtime 错误
        // 'level' =>  E_ALL, // 报告所有错误
        // 'level' =>  E_ALL & ~E_NOTICE, // 报告 E_NOTICE 之外的所有错误
    ],
]
```

> `level` 与 PHP 原生的 `error_reporting()` 函数参数一致。

抛出的异常类为 `\ErrorException`
