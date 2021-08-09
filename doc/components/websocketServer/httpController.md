# HTTP 控制器

在 WebSocket 服务器中，如果有需要可以在 HTTP 控制器中自行做握手处理。

> 仅 Swoole 需要

## 指定 WebSocket 数据处理器

在控制器中的方法上加上注解：

```php
@WSConfig(parserClass=\Imi\Server\DataParser\JsonArrayParser::class)
```

parserClass 指定的就是处理器类，作用就是接收到数据后自动解码为对象或数组，发送时自动将数组或对象编码为相应数据。

内置支持的类详见：<https://doc.imiphp.com/v2.0/components/server/dataParser.html>
