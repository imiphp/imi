# 错误异常处理

当程序出现错误或者异常时，我们一般会希望在开发时输出报错信息，在生产环境时隐藏详细的信息。

在 imi 中，提供了 Http 服务的错误异常默认处理器支持。

默认 Http 错误处理器：`Imi\Server\Http\Error\JsonErrorHandler`

## 指定默认处理器

配置文件中：

```php
return [
    'beans'    => [
        'HttpErrorHandler' => [
            // 指定默认处理器
            'handler' => \Imi\Server\Http\Error\JsonErrorHandler::class,
        ],
    ],
];
```

## 默认处理器参数设置

```php
return [
    'beans' => [
    \Imi\Server\Http\Error\JsonErrorHandler::class => [
            // debug 为 false时也显示错误信息
            'releaseShow' => false,
            // 取消继续抛出异常，也不会记录日志
            'cancelThrow' => true,
            // 异常时响应的 Http Code，默认 null，不设置
            'httpCode'    => 500,
        ],
    ],
];
```

## 编写处理器

如下代码所示，实现`IErrorHandler`接口，`handle()`方法返回值为true时则取消继续抛出异常。

```php
<?php
namespace Imi\Server\Http\Error;

use Imi\App;
use Imi\RequestContext;
use Imi\Server\View\Annotation\View;
use Imi\Util\Format\Json;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\RequestHeader;
use Imi\Server\Http\Error\IErrorHandler;

class JsonErrorHandler implements IErrorHandler
{
    /**
     * debug 为 false时也显示错误信息.
     */
    protected bool $releaseShow = false;

    /**
     * 取消继续抛出异常.
     */
    protected bool $cancelThrow = false;

    protected View $viewAnnotation;

    public function __construct()
    {
        $this->viewAnnotation = new View();
    }

    /**
     * {@inheritDoc}
     */
    public function handle(\Throwable $throwable): bool
    {
        if ($this->releaseShow || App::isDebug())
        {
            $data = [
                'message'   => $throwable->getMessage(),
                'code'      => $throwable->getCode(),
                'file'      => $throwable->getFile(),
                'line'      => $throwable->getLine(),
                'trace'     => explode(\PHP_EOL, $throwable->getTraceAsString()),
            ];
        }
        else
        {
            $data = [
                'success' => false,
                'message' => 'error',
            ];
        }
        $requestContext = RequestContext::getContext();
        /** @var \Imi\Server\View\Handler\Json $jsonView */
        $jsonView = $requestContext['server']->getBean('JsonView');
        $jsonView->handle($this->viewAnnotation, null, $data, $requestContext['response'] ?? null)->send();

        return $this->cancelThrow;
    }
}
```
