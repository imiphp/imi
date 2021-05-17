# UDP 控制器

## 定义

```php
<?php
namespace ImiDemo\UdpDemo\MainServer\Controller;

use Imi\ConnectContext;
use Imi\Server\Route\Annotation\Udp\UdpRoute;
use Imi\Server\Route\Annotation\Udp\UdpAction;
use Imi\Server\Route\Annotation\Udp\UdpController;

/**
 * 数据收发测试
 * @UdpController
 */
class Test extends \Imi\Controller\UdpController
{
	/**
	 * 登录
	 * 
	 * @UdpAction
	 * @UdpRoute({"action"="hello"})
	 * @return void
	 */
	public function hello()
	{
		return [
			'time'	=>	date($this->data->getFormatData()->format),
		];
	}

}
```

首先控制器类必须有`@UdpController`注解，对应动作必须有`@UdpAction`和`@UdpRoute`注解。

## 注解

### @UdpController

注释目标：类

表明一个类是控制器类

| 属性名称 | 说明 |
| ------------ | ------------ 
| singleton | 是否为单例控制器，默认为 null 时取 '@server.服务器名.controller.singleton' |
| server | 指定当前控制器允许哪些服务器使用。支持字符串或数组，默认为 null 则不限制 |

### @UdpRoute

指定 Udp 路由解析规则。

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
	// 项目根命名空间
	'namespace'	=>	'ImiDemo\UdpDemo',
	// 扫描目录
	'beanScan'	=>	[
	],
	// 主服务器配置，提供websocket服务
	'mainServer'	=>	[
		'namespace'	=>	'ImiDemo\UdpDemo\MainServer',
		'type'		=>	Type::UDP_SERVER,
		'host'		=>	'0.0.0.0',
		'port'		=>	8087,
		'configs'	=>	[
			'reactor_num'		=>	2,
			'worker_num'		=>	2,
			'task_worker_num'	=>	8,
		],
		// 数据处理器
		'dataParser'	=>	Imi\Server\DataParser\JsonObjectParser::class,
	],
}
```

**响应数据**

```php
return ['success'=>true];
```

### 分组发送

由于UDP的特性，所以不支持分组发送。如有需要，可根据实际场景自行实现分组。

### $data

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
 * 获取客户端信息
 * @return array
 */
public function getClientInfo();

// 格式同：https://wiki.swoole.com/#/server/methods?id=getclientinfo
```

