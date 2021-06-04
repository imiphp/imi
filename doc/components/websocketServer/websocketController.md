# WebSocket 控制器

所有从客户端发过来的数据都会打进控制器，我们开发项目时候，处理请求基本都是在控制器里做。

## 定义

```php
<?php
namespace ImiDemo\WebSocketDemo\MainServer\WSController\Index;

use Imi\ConnectionContext;
use Imi\Controller\WebSocketController;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSController;

/**
 * 数据收发测试
 * @WSController
 */
class Test extends WebSocketController
{
	/**
	 * 登录
	 * 
	 * @WSAction
	 * @WSRoute({"action"="login"})
	 * @return void
	 */
	public function login($data)
	{
		ConnectionContext::set('username', $data->username);
		$this->server->joinGroup('g1', $this->frame->getClientId());
		return ['success'=>true];
	}
}
```

首先控制器类必须有`@WSController`注解，对应动作必须有`@WSAction`和`@WSRoute`注解。

## 注解

### @WSController

注释目标：类

表明一个类是控制器类

| 属性名称 | 说明 |
| ------------ | ------------ 
| singleton | 是否为单例控制器，默认为 null 时取 '@server.服务器名.controller.singleton' |
| route | http 路由。如果设置，则只有握手指定 http 路由，才可以触发该 WebSocket 路由 |
| server | 指定当前控制器允许哪些服务器使用。支持字符串或数组，默认为 null 则不限制 |

通常：

```php
@WSController
```

指定匹配 http 路由：

```php
// 只有握手 /test 这个路径才可以触发该 WebSocket 动作
@WSController(route="/test")
```

### @WSRoute

指定 WebSocket 路由解析规则。

```php
// 解析 $data['action'] === 'login'
@WSRoute({"action"="login"})
// 解析 $data['a']['b']['c'] === 'login'
@WSRoute({"a.b.c"="login"})
// 解析 $data['a'] == '1' && $data['b'] == '2'
@WSRoute({"a"="1", "b"="2"})
```

当然对象也是支持的：

```php
// 解析 $data->a->b->c === 'login'
@WSRoute({"a.b.c"="login"})
```

指定匹配 http 路由：

```php
// 只有握手 /test 这个路径才可以触发该 WebSocket 动作
@WSRoute(condition={"action"="login"}, route="/test")
```

路由匹配成功，就会执行这个动作。

## 动作响应数据

### 响应当前这个请求

直接在方法中返回一个数组或对象，在Http 控制器中`@WSConfig`中设定的处理器，就会把这个转为对应数据响应给客户端。

```php
return ['success'=>true];
```

### 分组发送

```php
$this->server->groupCall('组名', 'push', ['success'=>true]);
```

当然，并不是每个请求都需要有响应数据，什么都不`return`或者`return null`就是不响应数据。

## 类属性

### $server

详见：<https://doc.imiphp.com/core/server.html>

### $frame

#### 方法

```php
/**
 * 获取客户端的socket id
 * @return int|string
 */
public function getClientId();
```

```php
/**
 * 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断
 * @return string
 */
public function getData();
```

```php
/**
 * 获取格式化后的数据，一般是数组或对象
 * @return mixed
 */
public function getFormatData();
```

```php
/**
 * WebSocket的OpCode类型，可以参考WebSocket协议标准文档
 * WEBSOCKET_OPCODE_TEXT = 0x1 ，文本数据
 * WEBSOCKET_OPCODE_BINARY = 0x2 ，二进制数据
 * @return int
 */
public function getOpcode();
```

```php
/**
 * 表示数据帧是否完整
 * @return boolean
 */
public function isFinish();
```

```php
/**
 * 获取 \Swoole\Websocket\Frame 对象
 * @return \Swoole\Websocket\Frame
 */
public function getSwooleWebSocketFrame(): \Swoole\Websocket\Frame;
```