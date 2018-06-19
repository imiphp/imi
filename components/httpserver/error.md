当程序出现错误或者异常时，我们一般会希望在开发时输出报错信息，在生产环境时隐藏详细的信息。

在 IMI 中，提供了 Http 服务的错误异常默认处理器支持。

默认 Http 错误处理器：`Imi\Server\Http\Error\JsonErrorHandler`

### 指定默认处理器

配置文件中：

```php
return [
	'beans'	=>	[
		'HttpErrorHandler'	=>	[
			// 指定默认处理器
			'handler'	=>	\Imi\Server\Http\Error\JsonErrorHandler::class,
		],
	],
];
```

### 处理器参数设置

```php
return [
	'beans'	=>	[
		\Imi\Server\Http\Error\JsonErrorHandler::class	=>	[
			// debug 为 false时也显示错误信息
			'releaseShow'	=>	false,
			// 取消继续抛出异常
			'cancelThrow'	=>	true,
		],
	],
];
```



### 编写处理器

如下代码所示，实现`IErrorHandler`接口，`handle()`方法返回值为true时则取消继续抛出异常。

```php
<?php
namespace Imi\Server\Http\Error;

use Imi\App;
use Imi\RequestContext;
use Imi\Util\Format\Json;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\RequestHeader;

class JsonErrorHandler implements IErrorHandler
{
	/**
	 * debug 为 false时也显示错误信息
	 * @var boolean
	 */
	protected $releaseShow = false;

	/**
	 * 取消继续抛出异常
	 * @var boolean
	 */
	protected $cancelThrow = true;

	public function handle(\Throwable $throwable): bool
	{
		if($this->releaseShow || App::isDebug())
		{
			$data = [
				'message'	=>	$throwable->getMessage(),
				'code'		=>	$throwable->getCode(),
				'file'		=>	$throwable->getFile(),
				'line'		=>	$throwable->getLine(),
				'trace'		=>	explode(PHP_EOL, $throwable->getTraceAsString()),
			];
		}
		else
		{
			$data = [
				'success'	=>	false,
				'message'	=>	'error',
			];
		}
		RequestContext::get('response')
		->withAddedHeader(RequestHeader::CONTENT_TYPE, MediaType::APPLICATION_JSON)
		->write(Json::encode($data))
		->send();
		return $this->cancelThrow;
	}
}
```
