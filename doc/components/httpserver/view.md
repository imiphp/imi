# 视图

在前面讲到的例子中，几乎都是直接对`$response`进行操作，然而实际上很少需要对其直接操作的情况。

在 imi 中可以使用视图来决定响应内容和格式，包括JSON、XML、模版渲染在内的 imi 认为都是视图，视图可以直接通过注解来设置。

```php
<?php
namespace Test;

use Imi\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\View\Annotation\HtmlView;
use Imi\Server\View\Annotation\View;

/**
 * 一个简单的控制器
 * @Controller
 * @View(renderType="json")
 * @HtmlView(baseDir="index/")
 */
class Index extends HttpController
{
       /**
        * 一个动作
        * @Action
        * @Route(url="/")
        * @View(renderType="html")
        * @HtmlView(template="index")
        */
        public function index()
        {
            $this->response->getBody()->write('hello imi!');
			return $this->response;
        }
}
```

如上代码所示，`@View`和`@HtmlView`注解可以写在类和方法的注释中。

`@HtmlView`注解的`baseDir`属性是模板基础路径，`/`开头为绝对路径。

类注解代表针对所有动作设定的视图配置，在单个方法上写注解，会覆盖类注解对应的配置。

## json

```php
/**
 * @Action
 * @View(renderType="json")
 */
public function index()
{
	// 数组
	$jsonData = [
		'id'	=>	1,
		'name'	=>	'imi',
	];
	// 对象
	// $jsonData = new stdClass;
	// $jsonData->name = 'imi';
	return $jsonData;
}
```

### 可选配置

```php
return [
	'beans'		=>	[
		'JsonView'	=>	[
			// json_encode 的参数值配置
			'options'	=>	\JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
			'depth'		=>	512,
		]
	]
];
```

## xml

```php
/**
 * @Action
 * @View(renderType="xml")
 */
public function index()
{
	// DOMDocument
	$xml = new \DOMDocument();
	$xml->loadXML($xmlString);
	
	// SimpleXMLElement
	$xml = \simplexml_load_string($xmlString);
	
	return $xml;
}
```

## 模版渲染

### 必选配置

```php
return [
	'beans'		=>	[
		'HtmlView'	=>	[
			'templatePath'	=>	'模版文件根路径',
			// 支持的模版文件扩展名，优先级按先后顺序
			// 'fileSuffixs'		=>	[
				'tpl',
				'html',
				'php'
			],
		]
	]
];
```

### 使用方式

#### 通过注解配置

```php
/**
 * @Action
 * @View(renderType="html")
 * @HtmlView(template="a/b")
 */
public function index()
{
	return [
		'content'	=>	'hello imi',
	];
}
```

#### 语句动态渲染

```php
/**
 * @Action
 */
public function index()
{
	return $this->__render('a/b', [
		'content'	=>	'hello imi',
	]);
}
```

#### 模版文件

`模版基础路径/a/b.html`

```html
<?=$content?>
```

运行结果：`hello imi`

imi 没有造模版引擎的轮子，是因为现在 PHP 渲染 HTML 的场景越来越少，如果有需要也可以自己集成其它开源模版引擎。

## 其它

在控制器-动作中，除了返回数据，你还可以直接返回`$this->response`，如：
```php
$this->response->getBody()->write('hello world');
return $this->response;
```

你还可以直接返回`@View`的注解类实例：
```php
return new \Imi\Server\View\Annotation\View([
	'template'	=>	'index',
	'renderType'=>	'html',
	'data'		=>	[
		'name'	=>	'imi',
	],
]);
```
