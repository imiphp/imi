# TCP 控制器

## 定义

```php
<?php
namespace ImiDemo\TcpDemo\MainServer\Controller;

use Imi\ConnectionContext;
use Imi\Server\TcpServer\Route\Annotation\TcpRoute;
use Imi\Server\TcpServer\Route\Annotation\TcpAction;
use Imi\Server\TcpServer\Route\Annotation\TcpController;

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
		ConnectionContext::set('username', $data->username);
		$this->server->joinGroup('g1', $this->data->getClientId());
		return ['action'=>'login', 'success'=>true];
	}
}
```

首先控制器类必须有`@TcpController`注解，对应动作必须有`@TcpAction`和`@TcpRoute`注解。

## 注解

### @TcpController

注释目标：类

表明一个类是控制器类

| 属性名称 | 说明 |
| ------------ | ------------ 
| singleton | 是否为单例控制器，默认为 null 时取 '@server.服务器名.controller.singleton' |
| server | 指定当前控制器允许哪些服务器使用。支持字符串或数组，默认为 null 则不限制 |

### @TcpRoute

指定 Tcp 路由解析规则。

```php
// 解析 $data['action'] === 'login'
@TcpRoute({"action"="login"})
// 解析 $data['a']['b']['c'] === 'login'
@TcpRoute({"a.b.c"="login"})
// 解析 $data['a'] == '1' && $data['b'] == '2'
@TcpRoute({"a"="1", "b"="2"})
```

当然对象也是支持的：

```php
// 解析 $data->a->b->c === 'login'
@TcpRoute({"a.b.c"="login"})
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

## 指定连接发送数据

以下代码写在控制器的代码中，总而言之，如果你要推送消息，你得拿到`SwooleServer`对象。

```php
// $server 对象是 \Swoole\Server类型
$server = $this->server->getSwooleServer();
// 指定连接
$clientId = 19260817;
$data = 'hello imi';

// 原样发送数据
$server->send($clientId, $data);

// 使用预定义的编码器，编码后发送数据
$server->send($clientId, $this->encodeMessage($data));
```

## 类属性

### $server

详见：<https://doc.imiphp.com/core/server.html>

### $data

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
 * 获取Reactor线程ID
 *
 * @return int
 */
public function getReactorId(): int;
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
