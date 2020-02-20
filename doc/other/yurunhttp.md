# YurunHttp

YurunHttp 是开源的 PHP HTTP 类库，支持链式操作，简单易用。

支持所有常见的 GET、POST、PUT、DELETE、UPDATE 等请求方式，支持 Http2、WebSocket、浏览器级别 Cookies 管理、上传下载、设置和读取 header、Cookie、请求参数、失败重试、限速、代理、证书等。

使用手册：<http://doc.yurunsoft.com/YurunHttp>

## 使用

```json
{
    "require": {
        "yurunsoft/yurun-http": "^4.0.0"
    }
}
```

### Swoole 协程模式

```php
<?php
use Yurun\Util\YurunHttp;
use Yurun\Util\HttpRequest;

// 设置默认请求处理器为 Swoole
YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Swoole::class);

// Swoole 处理器必须在协程中调用
go('test');

function test()
{
    $http = new HttpRequest;
    $response = $http->get('http://www.baidu.com');
    echo 'html:', PHP_EOL, $response->body();
}
```

### WebSocket Client

```php
YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Swoole::class);
go(function(){
    $url = 'ws://127.0.0.1:1234/';
    $http = new HttpRequest;
    $client = $http->websocket($url);
    if(!$client->isConnected())
    {
        throw new \RuntimeException('Connect failed');
    }
    $client->send('data');
    $recv = $client->recv();
    var_dump('recv:', $recv);
    $client->close();
});
```

### Http2 兼容用法

```php
$http = new HttpRequest;
$http->protocolVersion = '2.0'; // 这句是关键
$response = $http->get('https://wiki.swoole.com/');
```

Curl、Swoole Handler 都支持 Http2，但需要注意的是编译时都需要带上启用 Http2 的参数。

查看是否支持：

Curl: `php --ri curl`

Swoole: `php --ri swoole`

### Http2 全双工用法

> 该用法仅支持 Swoole

```php
$uri = new Uri('https://wiki.swoole.com/');

// 客户端初始化和连接
$client = new \Yurun\Util\YurunHttp\Http2\SwooleClient($uri->getHost(), Uri::getServerPort($uri), 'https' === $uri->getScheme());
$client->connect();

// 请求构建
$httpRequest = new HttpRequest;
$request = $httpRequest->header('aaa', 'bbb')->buildRequest($uri, [
    'date'  =>  $i,
], 'POST', 'json');

for($i = 0; $i < 10; ++$i)
{
    go(function() use($client, $request){
        // 发送（支持在多个协程执行）
        $streamId = $client->send($request);
        var_dump('send:' . $streamId);

        // 接收（支持在多个协程执行）
        $response = $client->recv($streamId, 3);
        $content = $response->body();
        var_dump($response);
    });
}
```