# EasyWechat

[TOC]

## 介绍

EasyWeChat 是一个开源的微信非官方 SDK。安装非常简单，因为它是一个标准的 Composer 包，这意味着任何满足下列安装条件的 PHP 项目支持 Composer 都可以使用它。

官网：<https://easywechat.com/>

## 代码示例

在 imi 中使用 EasyWechat 你需要做一些适配，下面是示例代码：

```php
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\ResponseHeader;
use Imi\Util\Stream\MemoryStream;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

// 配置及对象实例化请参考 EasyWechat 官方文档
// $app = ...
// $server = ...

// 从 imi psr-7 Request 绑定到 EasyWechat 的非标准 Request
$get = $this->request->getQueryParams();
$post = $this->request->getParsedBody();
$cookies = $this->request->getCookieParams();
$uploadFiles = $this->request->getUploadedFiles() ?? [];
$server = $this->request->getServerParams();
$body = (string) $this->request->getBody()->getContents();
$files = [];
/** @var \Imi\Server\Http\Message\UploadedFile $v */
foreach ($uploadFiles as $k => $v) {
    $files[$k] = [
        'error'    => $v->getError(),
        'name'     => $v->getClientFilename(),
        'size'     => $v->getSize(),
        'tmp_name' => $v->getTmpFileName(),
        'type'     => $v->getClientMediaType(),
    ];
}
$request = new Request($get, \is_array($post) ? $post : [], [], $cookies, $files, $server, $body);
$request->headers = new HeaderBag($this->request->getHeaders());
$app->rebind('request', $request);

// 做一些事情

$response = $server->serve();

// 将响应输出
return $this->response->setHeader(ResponseHeader::CONTENT_TYPE, MediaType::TEXT_XML)
                      ->setBody(new MemoryStream($response->getContent()));
```
