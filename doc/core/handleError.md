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
        'backtraceLimit' => 0 // Log 组件堆栈帧回溯数量限制，默认为 0 不限制回溯数量。
    ],
]
```

> `level` 与 PHP 原生的 `error_reporting()` 函数参数一致。
> `backtraceLimit` 与 PHP `debug_backtrace` 的 `limit` 参数一致，但由于框架需要，当值`>0`且`<6` 会重置为`6`。

抛出的异常类为 `\ErrorException`
