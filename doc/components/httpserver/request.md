# 请求类

[toc]

imi 中的 Request 对象，可以用于获取参数、请求头等，遵循 PSR-7 标准。

PSR-7: <https://www.php-fig.org/psr/psr-7/>

imi 在 PSR-7 基础上，为所有 `withXXX` 方法都加上了 `setXXX` 用法，方便快捷无心智负担。

## 获取 Request 对象

### 控制器

```php
$this->request;
```

### 请求上下文

```php
/** @var \Imi\Server\Http\Message\Contract\IHttpRequest $request */
$request = \Imi\RequestContext::get('request');
```

### 请求上下文代理

```php
/** @var \Imi\Server\Http\Message\Contract\IHttpRequest $request */
$request = \Imi\Server\Http\Message\Proxy\RequestProxy::__getProxyInstance();
```

## 操作 Request 对象

通常可以先获取 Request 对象，然后再调用对象的方法。

另外，你也可以直接通过请求上下文代理类，在任意地方使用，调用方式是静态方法调用：

```php
\Imi\Server\Http\Message\Proxy\RequestProxy::post('id');
```

### 获取 GET 参数

`public function get($name = null, $default = null)`

当`$name`为`null`时，返回全部

### 获取 POST 参数

`public function post($name = null, $default = null)`

### 获取 REQUEST 参数

request 数据包含 get/post/cookie

`public function request($name = null, $default = null)`

当`$name`为`null`时，返回全部

> 不支持 JSON 传参，请使用 `getParsedBody()`

### 获取 JSON/XML 传参数据

`public function getParsedBody()`

> 返回值是数组或对象

### 获取请求 Body

```php
$body = $this->request->getBody();
// 获取数据内容
// 方法一
$data = $body->getContents();
// 方法二
$data = (string) $body;
```

### 是否存在 GET 参数

`public function hasGet($name)`

### 是否存在 POST 参数

`public function hasPost($name)`

### 是否存在 REQUEST 参数

request 数据包含 get/post/cookie

`public function hasRequest($name)`

### 获取所有 Cookie

`public function getCookieParams()`

### 获取 Cookie 值

`public function getCookie($name, $default = null)`

### 获取所有请求头

`public function getHeaders()`

### 请求头是否存在，不区分大小写

`public function hasHeader($name)`

### 获取请求头，不区分大小写，支持同名，返回数组

`public function getHeader($name)`

### 获取请求头，不区分大小写，支持同名，返回字符串

`public function getHeaderLine($name)`

### 获取请求方法 (GET/POST等)

`public function getMethod()`

### 获取 HTTP 协议版本

`public function getProtocolVersion()`

### 获取请求地址

`public function getUri()`

> 协议会根据当前服务器是否启用 `ssl` 判断，支持协议：`http/https/ws/wss`
> URI结构`[scheme:]//[user-info@]host[:port][/path][?query][#fragment]`
> 当请求通过`nginx`转发时，此方法目前暂时无法正确判断`scheme`
> 可在`nginx`配置`location`中添加 `proxy_set_header X-Forwarded-Proto $scheme;`
> 通过获取请求头`$this->request->getHeaderLine('x-forwarded-proto');`来获取对应的`scheme`

### 获取应用请求地址

`public function getAppUri(?string $serverName = null)`

和 `getUri()` 不同的是，可以通过配置修改 `getUri()` 获取到的 Uri 里的 `host` 等参数。

适合用于替换生产环境中的 https、域名等参数。

需要在服务器配置中修改，详见对应容器服务器配置。

`$serverName` 默认不传则使用当前服务器。

### 获取 Swoole 服务器对象

`public function getServerInstance(): \Imi\Swoole\Server\Http\Server`

### 获取 Workerman 的 Worker 对象

`public function getWorker(): \Workerman\Worker`

### 获取 Workerman 的 http 请求对象

`public function getWorkermanRequest(): \Workerman\Protocols\Http\Request`

### 获取 Workerman 的连接对象

`public function getConnection(): \Workerman\Connection\TcpConnection`

### 获取上传的文件

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
    // 获取临时文件名
    $tmpFileName = $file->getTmpFileName();
}
```

### 获取 Server 信息

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

### 获取客户端IP地址

客户端直连服务端的情况下

```php
$address = $this->request->getClientAddress();
$address->getAddress(); // ip
$address->getPort(); // 端口
echo $address; // ip:端口
```

若使用nginx转发到服务端情况下

> 需在`nginx`配置`location`中添加`proxy_set_header X-Real-IP $remote_addr;`

```php
$address = $this->request->getHeaderLine('x-real-ip');
```

### 获取 Swoole Request 对象

```php
/** @var \Swoole\Http\Request $swooleRequest */
$swooleRequest = $this->request->getSwooleRequest();
```

## 绑定请求参数到控制器方法参数

### Action 方法参数

在控制器中，可以通过方法参数获取请求参数，imi 会自动绑定同名请求参数到方法参数。

imi `v2.1.47` 新增支持 `\Psr\Http\Message\UploadedFileInterface` 类型参数，可以直接获取上传的文件，并且 imi 底层会帮你做是否上传和成功的验证，失败会自动抛出异常。

```php
#[Action]
public function requestParam1(string $string, int $int, float $float, bool $bool, \Psr\Http\Message\UploadedFileInterface $file): array
{
    // 本地保存文件，$saveFileName 请改为自己的路径规则
    $saveFileName = '/var/www/html/upload/' . uniqid(true) . '.' . pathinfo($file->getClientFilename(), \PATHINFO_EXTENSION);
    $file->moveTo($saveFileName);

    // 获取临时文件名，可用于对象存储上传
    $tmpFileName = $file->getTmpFileName();

    // 获取文件内容，可用于对象存储上传
    $fileData = (string) $file->getStream();

    return compact('string', 'int', 'float', 'bool');
}
```

### RequestParam 注解

imi `v2.1.27` 引入的新注解。

`@RequestParam` 注解的 `name` 是指定参数来源。

你可以使用`$get`、`$post`、`$body`、`$headers`、`$cookie`、`$session`后面跟上`.参数名`指定参数，其中`$get`和`$post`自然不用多说，这`$body`的用处就是，比如参数是以`json`为`body`传给你的，他会自动给你`json_decode`，你用`$body`就可以指定了。

---

`@RequestParam` 注释注解写法支持写在方法上。

`#[RequestParam()]` PHP 原生注解写法支持写在方法和方法参数上。

写在方法参数上时，无需指定 `param` 参数。

---

`required` 表示是否为必选参数，默认为 `true`。

`default` 表示默认值，当 `required` 为 `false` 时有效，默认值是 `null`。

---

**代码示例：**

```php
#[
    Action,
    RequestParam(name: '$get.id', param: 'id2'),
    RequestParam(name: '$get.id3', param: 'id3', required: false, default: 'imi 666')
]
public function requestParam1(int $id, int $id2, string $id3): array
{
    return [
        'id'  => $id,
        'id2' => $id2,
        'id3' => $id3,
    ];
}

#[Action]
public function requestParam2(
    int $id,
    #[RequestParam(name: '$get.id')]
    int $id2,
    #[RequestParam(name: '$get.id3', required: false, default: 'imi niubi')]
    string $id3
    ): array {
    return [
        'id'  => $id,
        'id2' => $id2,
        'id3' => $id3,
    ];
}
```

### ExtractData 注解

将在 imi 3.0 中废弃，推荐使用 `RequestParam` 注解。

**代码示例：**

```php
/**
 * http参数验证测试
 * 
 * @return void
 */
#[
    Action,
    ExtractData(name: '$get.id', to: 'id'),
    ExtractData(name: '$get.name', to: 'name'),
    ExtractData(name: '$get.age', to: 'age'),
]
public function httpValidation($id, $name, $age)
{
    return compact('id', 'name', 'age');
}
```
