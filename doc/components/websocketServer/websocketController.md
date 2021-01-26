# WebSocket 控制器

## 定义

```php
<?php
namespace ImiDemo\WebSocketDemo\MainServer\WSController\Index;

use Imi\ConnectContext;
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
		ConnectContext::set('username', $data->username);
		$this->server->joinGroup('g1', $this->frame->getFd());
		return ['success'=>true];
	}
}
```

首先控制器类必须有`@WSController`注解，对应动作必须有`@WSAction`和`@WSRoute`注解。

## 注解

### @WSController

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

#### 方法

**getSwooleServer**

获取Swoole的Server对象


```php
/**
 * 组是否存在
 *
 * @param string $groupName
 * @return boolean
 */
public function hasGroup(string $groupName);
```

```php
/**
 * 创建组，返回组对象
 *
 * @param string $groupName
 * @param integer $maxClients
 * @return \Imi\Server\Group\Group
 */
public function createGroup(string $groupName, int $maxClients = -1);
```

```php
/**
 * 获取组对象，不存在返回null
 *
 * @param string $groupName
 * @return \Imi\Server\Group\Group|null
 */
public function getGroup(string $groupName);
```

```php
/**
 * 加入组，组不存在则自动创建
 *
 * @param string $groupName
 * @param integer $fd
 * @return void
 */
public function joinGroup(string $groupName, int $fd);
```

```php
/**
 * 离开组，组不存在则自动创建
 *
 * @param string $groupName
 * @param integer $fd
 * @return void
 */
public function leaveGroup(string $groupName, int $fd);
```

```php
/**
 * 调用组方法
 *
 * @param string $groupName
 * @param string $methodName
 * @param mixed ...$args
 * @return array
 */
public function groupCall(string $groupName, string $methodName, ...$args);
```

```php
/**
 * 获取所有组列表
 *
 * @return \Imi\Server\Group\Group[]
 */
public function getGroups(): array;
```
### $frame

#### 方法

```php
/**
 * 获取客户端的socket id
 * @return int
 */
public function getFd(): int;
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