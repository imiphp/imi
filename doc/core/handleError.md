# 错误转为异常捕获

imi 框架底层支持将错误转为异常，可以通过 `try...catch` 来捕获。

在 `config.php` 中的 `beans` 配置

```php
[
    'ErrorLog'  =>  [
        // 'level' =>  E_ALL, // 报告所有错误，这是默认值
        // 'level' =>  E_ALL & ~E_NOTICE, // 报告 E_NOTICE 之外的所有错误

        // 错误捕获级别，捕获到的错误都会做处理，此为默认值
        'catchLevel' => E_ALL | E_STRICT,
        // 抛出异常的错误级别，除此之外全部记录日志，此为默认值
        'exceptionLevel' => E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR | E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING | E_USER_WARNING,
    ],
]
```

> 错误级别参考：<https://www.php.net/manual/zh/errorfunc.constants.php>

抛出的异常类为 `\ErrorException`
