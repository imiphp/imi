# 服务器配置

[toc]

```php
'fpmServer' => [
    // $request->getAppUri() 参数替换，每个参数都是可选项
    // 下面例子最终获取到的 Uri 为：https://root:123@imi-test:1234/test?id=666#test
    'appUri' => [
        'host'     => 'imi-test',   // 主机名
        'port'     => 1234,         // 端口
        'scheme'   => 'https',      // 协议
        'user'     => 'root',       // 用户名
        'pass'     => '123',        // 密码
        'path'     => '/test',      // 路径
        'query'    => 'id=666',     // 查询参数
        'fragment' => 'test',       // 锚点
    ],
    // 也支持回调
    'appUri' => function(\Imi\Util\Uri $uri) {
        return $uri->withHost('imi-test');
    },
],
```
