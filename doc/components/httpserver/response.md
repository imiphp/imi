# 响应类

[toc]

imi 中的 Response 对象，可以用于响应内容，遵循 PSR-7 标准。

PSR-7: <https://www.php-fig.org/psr/psr-7/>

imi 在 PSR-7 基础上，为所有 `withXXX` 方法都加上了 `setXXX` 用法，方便快捷无心智负担。

## 获取 Response 对象

### 控制器

```php
$this->response;
```

### 请求上下文

```php
/** @var \Imi\Server\Http\Message\Contract\IHttpResponse $response */
$response = \Imi\RequestContext::get('response');
```

### 请求上下文代理

```php
/** @var \Imi\Server\Http\Message\Contract\IHttpResponse $response */
$response = \Imi\Server\Http\Message\Proxy\ResponseProxy::__getProxyInstance();
```

## 操作 Response 对象

通常可以先获取 Response 对象，然后再调用对象的方法。

另外，你也可以直接通过请求上下文代理类，在任意地方使用，调用方式是静态方法调用：

```php
\Imi\Server\Http\Message\Proxy\ResponseProxy::setStatus(404);
```

### 重定向

`public function redirect($url, $status = StatusCode::FOUND)`

`$status` 是状态码，默认302，可以使用`StatusCode::XXX`常量

### 设置Cookie

`public function withCookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)`

### 发送所有响应数据

`public function send()`

### 发送文件，一般用于文件下载

```php
/**
 * 发送文件，一般用于文件下载.
 *
 * @param string      $filename       要发送的文件名称，文件不存在或没有访问权限sendfile会失败
 * @param string|null $contentType    Content-Type 响应头，不填则自动识别
 * @param string|null $outputFileName 下载文件名，不填则自动识别，如：123.zip
 * @param int         $offset         上传文件的偏移量，可以指定从文件的中间部分开始传输数据。此特性可用于支持断点续传。
 * @param int         $length         发送数据的尺寸，默认为整个文件的尺寸
 *
 * @return static
 */
public function sendFile(string $filename, ?string $contentType = null, ?string $outputFileName = null, int $offset = 0, int $length = 0): self
```

**例：**

```php
/**
 * @Action
 */
public function downloadFile()
{
    $this->response->sendFile(__FILE__); // 下载当前文件，实际上你可以指定服务器上的文件
}
```

### 发送图片

一般可用于二维码场景

```php
/**
 * @Action
 */
public function image()
{
    // 生成图片
    $img = imagecreatetruecolor(256, 256);
    $color = imagecolorallocate($img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
    imagefill($img, 0, 0, $color);

    // 生成图片二进制到变量
    ob_start();
    imagejpeg($img);
    $imgContent = ob_get_clean();

    // 输出响应头和图片二进制内容
    $this->response->setHeader(\Imi\Util\Http\Consts\ResponseHeader::CONTENT_TYPE, \Imi\Util\Http\Consts\MediaType::IMAGE_JPEG)
                    ->getBody()->write($imgContent);
    return $this->response;
}
```

### 响应头是否可写

`public function isHeaderWritable()`

### 响应主体是否可写

`public function isBodyWritable()`

### 获取swoole响应对象

`public function getSwooleResonse(): \Swoole\Http\Response`

### 获取对应的服务器

`public function getServerInstance(): \Imi\Swoole\Server\Contract\ISwooleServer`

### 设置状态码

`public function withStatus($code, $reasonPhrase = '')`

### 设置返回头

`public function withHeader($name, $value)`

```php
$reponse->withHeader('test', 'v1');
$reponse->withHeader('test', ['v2', 'v3']);
// 最终header中test为v2,v3
```

### 添加返回头

`public function withAddedHeader($name, $value)`

```php
$reponse->withAddedHeader('test', 'v1');
$reponse->withAddedHeader('test', ['v2', 'v3']);
// 最终header中test为v1,v2,v3
```

### 获取 Trailer 列表

`public function getTrailers(): array`

> Trailer 仅 Http2 中使用

### Trailer 是否存在

`public function hasTrailer(string $name): bool`

### 获取 Trailer 值

`public function getTrailer(string $name): ?string`

### 设置 Trailer 返回新对象

`public function withTrailer(string $name, string $value): self`

### 设置 Trailer

`public function setTrailer(string $name, string $value): self`

### 获取 Swoole Response 对象

```php
/** @var \Swoole\Http\Response $swooleResponse */
$swooleResponse = $this->response->getSwooleResponse();
```

### 获取 Workerman 的 http 响应对象

`public function getWorkermanResponse(): \Workerman\Protocols\Http\Response`
