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

#### 获取 GET 参数

`public function get($name = null, $default = null)`

当`$name`为`null`时，返回全部

#### 获取 POST 参数

`public function post($name = null, $default = null)`

当`$name`为`null`时，返回全部

#### 是否存在 GET 参数

`public function hasGet($name)`

#### 是否存在 POST参数

`public function hasPost($name)`

#### 获取所有 Cookie

`public function getCookieParams()`

#### 获取 Cookie 值

`public function getCookie($name, $default = null)`

#### 获取所有请求头

`public function getHeaders()`

#### 请求头是否存在，不区分大小写

`public function hasHeader($name)`

#### 获取请求头，不区分大小写，支持同名，返回数组

`public function getHeader($name)`

#### 获取请求头，不区分大小写，支持同名，返回字符串

`public function getHeaderLine($name)`

#### 获取请求方法 (GET/POST等)

`public function getMethod()`

#### 获取 HTTP 协议版本

`public function getProtocolVersion()`

#### 获取请求地址

`public function getUri()`

#### 获取 IMI 中对应服务器的对象

`public function getServerInstance(): \Imi\Server\Http\Server`

### $response

响应对象，遵循 PSR-7 标准。

直接对该对象操作无效，需要如下使用才可。

1. 操作后赋值：
```php
public function action()
{
	$this->response = $this->response->write('hello imi!');
}
```
2. 操作后返回
```php
public function action()
{
	return $this->response->write('hello imi!');
}
```
