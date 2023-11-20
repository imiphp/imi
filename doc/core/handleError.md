# 全局异常处理

[toc]

## 异常处理器配置

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
        /**
         * 异常事件处理器数组
         * @var array<class-string<Imi\Log\IErrorEventHandler>>
         */
        'errorEventHandlers' => [];
    ],
]
```

## 错误转为异常捕获

imi 框架底层支持将错误转为异常（通过`catchLevel`选项控制），可以通过 `try...catch` 来捕获。

> 错误级别参考：<https://www.php.net/manual/zh/errorfunc.constants.php>

抛出的异常类为 `\ErrorException`

## 全局异常处理器

支持通过`errorEventHandlers`数组声明多个全局异常事件处理器，每个异常处理器必须继承`Imi\Log\AbsErrorEventHandler`，并实现`handleError`,`handleException`方法。
多个异常处理器将按顺序执行，可调用方法`stopPropagation`取消后续异常处理器执行并阻止系统默认的异常处理。

> 请务必确保异常处理器内不要再次抛出异常，做好异常捕获安全处理。

### Demo

```php
# 当 catchLevel 设置为 E_ALL 时，添加以下处理器配合处理错误通知

<?php

declare(strict_types=1);

namespace Imi\App;

use Imi\Log\AbstractErrorEventHandler;
use Imi\Log\Log;
use Psr\Log\LogLevel;

class ErrorEventHandler extends AbstractErrorEventHandler
{
    public function handleError(int $errNo, string $errStr, string $errFile, int $errLine): void
    {
        if (str_contains($errFile, '/phpunit/src/'))
        {
            // 当前错误与用户代码无关错误且不影响程序正常执行，阻止其抛出异常并打印常规日志
            $this->stopPropagation();

            Log::log(LogLevel::INFO, $errStr);
        }
    }

    public function handleException(\Throwable $throwable): void
    {
        // 可以处理更多异常状况...
    }
}

```

