# 控制器

如下代码所示，一个最简单的控制器代码。

## 基本控制器

```php
<?php
namespace Test;

use Imi\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;

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

## 单例控制器

用法完全一致，适合用于单例控制器场景，对象内部协程上下文自动切换

```php
<?php
namespace Test;

use Imi\Controller\SingletonHttpController;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;

/**
 * 一个简单的控制器
 * @Controller(singleton=true)
 */
class Index extends SingletonHttpController
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

该对象同样可以用如下方法全局调用：

```php
/** @var \Imi\Server\Http\Message\Request $request */
$request = \Imi\RequestContext::get('request');
```

#### 获取 GET 参数

`public function get($name = null, $default = null)`

当`$name`为`null`时，返回全部

#### 获取 POST 参数

`public function post($name = null, $default = null)`

#### 获取 REQUEST 参数

request 数据包含 get/post/cookie

`public function request($name = null, $default = null)`

当`$name`为`null`时，返回全部

### 获取 JSON/XML 传参数据

`public function getParsedBody()`

#### 是否存在 GET 参数

`public function hasGet($name)`

#### 是否存在 POST 参数

`public function hasPost($name)`

#### 是否存在 REQUEST 参数

request 数据包含 get/post/cookie

`public function hasRequest($name)`

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

> 协议会根据当前服务器是否启用 `ssl` 判断，支持协议：`http/https/ws/wss`

#### 获取 imi 中对应服务器的对象

`public function getServerInstance(): \Imi\Swoole\Server\Http\Server`

#### 获取上传的文件

`public function getUploadedFiles()`

返回值为`Imi\Server\Http\Message\UploadedFile数组`

简单示例：

```php
foreach ($request->getUploadedFiles() as $k => $file)
{
    if (0 === $file->getError())
    {
        $file->moveTo('上传文件保存路径');
    }
    else
    {
        throw new \RuntimeException(sprintf('上传文件失败，错误码：%s', $file->getError()));
    }
}
```

#### 获取 Server 信息

```php
var_dump($this->request->getServerParams());
var_dump($this->request->getServerParam('path_info'));
```

输出：

```php
array(11) {
  ["request_method"]=>
  string(3) "GET"
  ["request_uri"]=>
  string(47) "/xxx.html"
  ["path_info"]=>
  string(47) "/xxx.html"
  ["request_time"]=>
  int(1538010416)
  ["request_time_float"]=>
  float(1538010417.6185)
  ["server_port"]=>
  int(8080)
  ["remote_port"]=>
  int(62687)
  ["remote_addr"]=>
  string(9) "127.0.0.1"
  ["master_time"]=>
  int(1538010416)
  ["server_protocol"]=>
  string(8) "HTTP/1.1"
  ["server_software"]=>
  string(18) "swoole-http-server"
}
string(47) "/xxx.html"
```

#### 获取客户端IP地址

```php
$ip = $this->request->getServerParam('remote_addr');
```

#### 获取 Swoole Request 对象

```php
/** @var \Swoole\Http\Request $swooleRequest */
$swooleRequest = $this->request->getSwooleRequest();
```

### $response

响应对象，遵循 PSR-7 标准。

该对象同样可以用如下方法全局调用：

```php
/** @var \Imi\Server\Http\Message\Response $request */
$response = \Imi\RequestContext::get('response');
```

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

#### 重定向

`public function redirect($url, $status = StatusCode::FOUND)`

`$status` 是状态码，默认302，可以使用`StatusCode::XXX`常量

#### 输出内容

`public function write(string $content)`

#### 清空输出缓冲区

`public function clear()`

#### 设置Cookie

`public function withCookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)`

#### 发送所有响应数据

`public function send()`

#### 发送文件，一般用于文件下载

```php
/**
 * 发送文件，一般用于文件下载
 * @param string $filename 要发送的文件名称，文件不存在或没有访问权限sendfile会失败
 * @param integer $offset 上传文件的偏移量，可以指定从文件的中间部分开始传输数据。此特性可用于支持断点续传。
 * @param integer $length 发送数据的尺寸，默认为整个文件的尺寸
 * @return static
 */
public function sendFile(string $filename, int $offset = 0, int $length = 0)
```

#### 是否已结束请求

`public function isEnded()`

#### 获取swoole响应对象

`public function getSwooleResonse(): \swoole_http_response`

#### 获取对应的服务器

`public function getServerInstance(): \Imi\Swoole\Server\Http\Server`

#### 设置状态码

`public function withStatus($code, $reasonPhrase = '')`

#### 设置返回头

`public function withHeader($name, $value)`

```php
$reponse->withHeader('test', 'v1');
$reponse->withHeader('test', ['v2', 'v3']);
// 最终header中test为v2,v3
```

#### 添加返回头

`public function withAddedHeader($name, $value)`
```php
$reponse->withAddedHeader('test', 'v1');
$reponse->withAddedHeader('test', ['v2', 'v3']);
// 最终header中test为v1,v2,v3
```

#### 获取 Swoole Response 对象

```php
/** @var \Swoole\Http\Response $swooleResponse */
$swooleResponse = $this->request->getSwooleResponse();
```
