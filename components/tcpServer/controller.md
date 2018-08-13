# TCP 控制器

## 定义

```php
<?php
namespace ImiDemo\TcpDemo\MainServer\Controller;

use Imi\ConnectContext;
use Imi\Server\Route\Annotation\Tcp\TcpRoute;
use Imi\Server\Route\Annotation\Tcp\TcpAction;
use Imi\Server\Route\Annotation\Tcp\TcpController;

/**
 * 数据收发测试
 * @TcpController
 */
class Test extends \Imi\Controller\TcpController
{
	/**
	 * 登录
	 * 
	 * @TcpAction
	 * @TcpRoute({"action"="login"})
	 * @return void
	 */
	public function login($data)
	{
		ConnectContext::set('username', $data->username);
		$this->server->joinGroup('g1', $this->data->getFd());
		return ['action'=>'login', 'success'=>true];
	}
}
```

首先控制器类必须有`@TcpController`注解，对应动作必须有`@TcpAction`和`@TcpRoute`注解。

## 注解

### @TcpRoute

指定 Tcp 路由解析规则。

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

路由匹配成功，就会执行这个动作。

## 动作响应数据

### 响应当前这个请求

直接在方法中返回一个数组或对象，在服务器配置设定的处理器，就会把这个转为对应数据响应给客户端。

**配置文件**

```php
return [
	// 主服务器配置，提供websocket服务
	'mainServer'	=>	[
		'namespace'	=>	'ImiDemo\TcpDemo\MainServer',
		'type'		=>	Type::TCP_SERVER,
		// 'host'		=>	'0.0.0.0',
		'port'		=>	8085,
		// 'mode'		=>	SWOOLE_BASE,
		// 'sockType'	=>	SWOOLE_SOCK_TCP,
		'configs'	=>	[
			'reactor_num'		=>	2,
			'worker_num'		=>	2,
			'task_worker_num'	=>	8,
			// EOF自动分包
			'open_eof_split'	=>	true, //打开EOF_SPLIT检测
			'package_eof'		=>	"\r\n", //设置EOF
		],
		// 数据处理器
		'dataParser'	=>	\ImiDemo\TcpDemo\MainServer\Parser\JsonObjectParser::class,
	],
}
```

**响应数据**

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
 * 获取Reactor线程ID
 *
 * @return int
 */
public function getReactorID(): int;
```

## 控制器类方法

### encodeMessage

```php
/**
 * 编码消息，把数据编码为发送给客户端的格式
 *
 * @param mixed $data
 * @return mixed
 */
protected function encodeMessage($data)
```
