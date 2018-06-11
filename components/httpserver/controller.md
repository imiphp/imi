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

| 属性名称 | 说明 |
| ------------ | ------------ 
| prefix | 动作配置的访问地址前缀，如果动作配置的访问地址规则以`/`开始，则本属性无效 |

#### @Route

注释目标：方法

| 属性名称 | 说明 |
| ------------ | ------------ 
| url | 请求地址规则。<br/>以`/`开头则忽视`@Controller`中的`prefix`<br/>支持代入`{name}`形式变量用于占位符，匹配出来的值可以作为参数传入动作 |
| method | 如果设置了`method`，则请求方法必须在`method`列表中才可以进到动作。<br/>支持字符串和数组。如：`"GET"`或`{"GET", "POST"}` |
| domain |  |
| paramsGet |  |
| paramsPost |  |
| header |  |
| requestMime |  |
| responseMime |  |

#### @Action

## 配置
