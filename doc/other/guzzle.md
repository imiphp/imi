# Guzzle

Guzzle 是一个 PHP 的 HTTP 客户端，用来轻而易举地发送请求，并集成到我们的WEB服务上。

Guzzle 被国内外广泛用于各种第三方 SDK 中，如果你正在寻找某某 SDK 的协程化支持，那么你来对啦！

宇润曾经申请将 `SwooleHttpClientHandler` 集成到 `Guzzle` 核心，并且引入一个全局默认请求器的设置功能，由于一些原因并没有通过。

> 传送门：<https://github.com/guzzle/guzzle/pull/2128>

上有政策，下有对策，于是就有了 [Guzzle-Swoole](https://github.com/Yurunsoft/Guzzle-Swoole)

当然，如果你是希望自己编写 http 请求代码，这里更推荐使用：[YurunHttp](yurunhttp.html)

## 介绍

让 Guzzle 支持 Swoole 协程，这个项目目的就是这么简单明了！

Guzzle-Swoole 是 Guzzle 的处理器（Handler），并没有对 Guzzle 本身代码进行修改，理论上可以兼容后续版本。

支持 Ring Handler，可以用于 `elasticsearch/elasticsearch` 等包中。

## 使用

Composer:`"yurunsoft/guzzle-swoole":"~2.0"`

### 全局设定处理器

```php
<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Yurun\Util\Swoole\Guzzle\SwooleHandler;
use GuzzleHttp\DefaultHandler;

DefaultHandler::setDefaultHandler(SwooleHandler::class);

go(function(){
    $client = new Client();
    $response = $client->request('GET', 'http://www.baidu.com', [
        'verify'    =>  false,
    ]);
    var_dump($response->getStatusCode());
});

```

### 手动指定 Swoole 处理器

```php
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Yurun\Util\Swoole\Guzzle\SwooleHandler;

go(function(){
    $handler = new SwooleHandler();
    $stack = HandlerStack::create($handler);
    $client = new Client(['handler' => $stack]);
    $response = $client->request('GET', 'http://www.baidu.com', [
        'verify'    =>  false,
    ]);
    var_dump($response->getBody()->__toString(), $response->getHeaders());
});
```

更加详细的示例代码请看`test`目录下代码。
