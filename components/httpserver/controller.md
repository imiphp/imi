IMI 框架同时支持注解和配置两种方式设置路由。

## 注解

如下代码所示，一个最简单的控制器代码。

```php
<?php
namespace Test;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;

/**
 * 一个简单的控制器
 * @Controller
 */
class Index extends HttpController
{
	/**
	 * 一个动作
	 * @Action
	 * @Route(url="/")
	 */
	public function index()
	{
		return $this->response->write('hello imi!');
	}
}
```

访问地址：`http://localhost:{port}/`
输出内容：
```
hello imi!
```

### 注解说明

#### @Controller

注释目标：类

表明一个类是控制器类

| 属性名称 | 说明 |
| ------------ | ------------ 
| prefix | 动作配置的访问地址前缀，如果动作配置的访问地址规则以`/`开始，则本属性无效 |

#### @Route

注释目标：方法

指定路由规则

| 属性名称 | 说明 |
| ------------ | ------------ 
| url | 请求地址规则。<br/>以`/`开头则忽视`@Controller`中的`prefix`<br/>支持代入`{name}`形式占位符，匹配出来的值可以作为参数传入动作 |
| method | 如果设置了`method`，则请求方法必须在`method`列表中才可以进到动作。<br/>支持字符串和数组。如：`"GET"`或`{"GET", "POST"}` |
| domain | 判断域名，只有符合条件才允许访问。<br/>支持字符串和数组，支持`{name}`形式占位符，可以作为参数传入动作 |
| paramsGet | 判断`GET`参数，只有符合条件才允许访问。<br/>可以是字符串或数组。<br/>`id=100`必须包含id，并且值为100<br/>`id!=100`或`id<>100`必须包含id，并且值不为100<br/>`id`必须包含id参数<br/>`!id`必须不包含id参数</br>`["id" => "\d+"]`支持正则</br> |
| paramsPost | 判断`POST`参数，用法同`paramsGet` |
| header | 判断请求头，用法同`paramsGet` |
| requestMime | 请求的mime类型判断<br/>判断请求头中的Content-Type中是否包含这些mime类型之一<br/>支持字符串和字符串数组<br/> |
| responseMime |  |

#### @Action

注释目标：方法

表明一个方法是动作

属性：无

## 配置

