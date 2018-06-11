如下代码所示，一个最简单的控制器代码。

```php
<?php
namespace Test;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;

/**
 * 一个简单的控制器
 * @Controller
 */
class Index extends HttpController
{
	/**
	 * 一个动作
	 * @Action
	 * @Route(url="/")
	 */
	public function index()
	{
		return $this->response->write('hello imi!');
	}
}
```

访问地址：`http://localhost:{port}/`
输出内容：
```
hello imi!
```

## 属性

### $request

请求信息对象，可以用于获取参数、请求头等，遵循 PSR-7 标准。

### $response

响应对象，遵循 PSR-7 标准。

直接对该对象操作无效，需要如下使用才可。

1. 操作后赋值：
```
public function action()
{
	$this->response = $this->response->write('hello imi!');
}
```
2. 操作后返回
```
public function action()
{
	return $this->response->write('hello imi!');
}
```
