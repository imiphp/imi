# 错误异常处理

[toc]

当程序出现错误或者异常时，我们一般会希望在开发时输出报错信息，在生产环境时隐藏详细的信息。

在 imi 中，提供了 Tcp 服务的错误异常默认处理器支持。

默认 Tcp 错误处理器：无

## 指定默认处理器

配置文件中：

```php
return [
    'beans'    => [
        'TcpErrorHandler' => [
            // 指定默认处理器
            'handler' => \app\Error\TcpErrorHandler::class,
        ],
    ],
];
```

## 编写处理器

如下代码所示，实现`IErrorHandler`接口，`handle()`方法返回值为true时则取消继续抛出异常。

```php
<?php
namespace app\Error;

use Imi\Server\TcpServer\Error\IErrorHandler;

class TcpErrorHandler implements IErrorHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Throwable $throwable): bool
    {
        // 做处理
        return true;
    }
}
```
