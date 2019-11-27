# Http2

imi v1.0.20 新增支持开发 Http2 服务。

具体开发方法和 Http、WebSocket 类似。

需要除了需要加配置外，请求响应对象是使用 Http 的对象。

并且可以像开发 WebSocket 一样使用请求上下文存储数据，因为 Http2 是以全双工长连接的方式工作的。

## 配置方法

在项目 `config.php` 中的服务器配置中加入：

```php
'configs'   =>  [
    'open_http2_protocol'   =>  true,
    // 下面是启用 https，如果需要就配置
    // 'ssl_cert_file'     =>  '/server.crt',
    // 'ssl_key_file'      =>  '/server.key',
],
```

其它用法参考 Http、WebSocket 即可。
