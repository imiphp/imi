# 路由

定义路由规则，让 imi 可以根据请求，导航到你写的控制器里。

## 启用路由

服务器配置文件中加入中间件：

```php
return [
    'beans' =>  [
        'HttpDispatcher'    =>  [
            'middlewares'   =>  [
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
    ],
];
```

通过配置注入形式，实现非常灵活的配置，你甚至可以自己另外实现路由中间件，而不用被框架自带的中间件所影响，用哪些中间件都在你的掌控之中！

## 路由配置

```php
return [
    'beans' => [
        'HttpRoute' => [
            // url匹配缓存数量，默认1024
            'urlCacheNumber' => 1024,
            // 全局忽略 URL 路由大小写
            'ignoreCase'     => false,
            // 全局支持智能尾部斜杠，无论是否存在都匹配
            'autoEndSlash'   => false,
        ],
    ],
];
```

## 注解说明

### @Controller

类名：`\Imi\Server\Http\Route\Annotation\Controller`

注释目标：类

表明一个类是控制器类

| 属性名称 | 说明 |
| ------------ | ------------ 
| prefix | 动作配置的访问地址前缀，如果动作配置的访问地址规则以`/`开始，则本属性无效 |
| singleton | 是否为单例控制器，默认为 null 时取 '@server.服务器名.controller.singleton' |
| server | 指定当前控制器允许哪些服务器使用。支持字符串或数组，默认为 null 则不限制 |

### @Route

类名：`\Imi\Server\Http\Route\Annotation\Route`

注释目标：方法

指定路由规则

| 属性名称 | 说明 |
| ------------ | ------------ 
| url | 请求地址规则。<br>以`/`开头则忽视`@Controller`中的`prefix`<br>为空或以`./`开头则在`@Controller`中的`prefix`后加上路由定义<br>支持代入`{name}`形式占位符，匹配出来的值可以作为参数传入动作<br>支持正则写法：`{id:\d+}` |
| method | 如果设置了`method`，则请求方法必须在`method`列表中才可以进到动作。<br>支持字符串和数组。如：`"GET"`或`{"GET", "POST"}` |
| domain | 判断域名，只有符合条件才允许访问。<br>支持字符串和数组，支持`{name}`形式占位符，可以作为参数传入动作 |
| paramsGet | 判断`GET`参数，只有符合条件才允许访问。<br>可以是字符串或数组。<br>`id=100`必须包含id，并且值为100<br>`id!=100`或`id<>100`必须包含id，并且值不为100<br>`id`必须包含id参数<br>`!id`必须不包含id参数</br>`["id" => "\d+"]`支持正则</br> |
| paramsPost | 判断`POST`参数，用法同`paramsGet` |
| paramsBody | 判断 JSON、XML 参数，用法同`paramsGet` |
| paramsBodyMultiLevel | JSON、XML参数条件支持以 . 作为分隔符，支持多级参数获取，默认为`true` |
| header | 判断请求头，用法同`paramsGet` |
| requestMime | 请求的mime类型判断<br>判断请求头中的Content-Type中是否包含这些mime类型之一<br>支持字符串和字符串数组<br> |
| ignoreCase | 忽略请求地址大小写<br>`null`-取HttpRoute中默认值<br>`true`-忽略大小写<br>`false`-严格判断 |
| autoEndSlash | 智能尾部斜杠，无论是否存在都匹配<br>`null`-取HttpRoute中默认值<br>`true`-忽略大小写<br>`false`-严格判断 |

### @Action

类名：`\Imi\Server\Http\Route\Annotation\Action`

注释目标：方法

表明一个方法是动作

属性：无

---

如下代码所示，一个最简单的控制器+路由注解代码。

```php
<?php
namespace Test;

use Imi\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;

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
        $this->response->getBody()->write('hello imi!');
        return $this->response;
    }
}
```

访问地址：`http://localhost:{port}/`
输出内容：
```
hello imi!
```

## 获取当前路由解析结果 (`routeResult`)

```php
$routeResult = RequestContext::get('routeResult');
```

`$routeResult` 定义：

```php
/**
 * 路由ID.
 */
public int $id = 0;

/**
 * 路由配置项.
 *
 * @var \Imi\Server\Http\Route\RouteItem
 */
public RouteItem $routeItem;

/**
 * 参数.
 */
public array $params = [];

/**
 * 回调.
 *
 * @var callable
 */
public $callable;
```

`$routeResult->routeItem` 定义：

```php
/**
 * 注解.
 */
public Route $annotation;

/**
 * 回调.
 *
 * @var callable|\Imi\Server\Route\RouteCallable
 */
public $callable;

/**
 * 中间件列表.
 */
public array $middlewares = [];

/**
 * WebSocket 配置.
 *
 * @var WSConfig
 */
public ?WSConfig $wsConfig = null;

/**
 * 其它配置项.
 */
public array $options = [];

/**
 * 视图注解.
 */
public View $view;

/**
 * 视图配置注解.
 */
public ?BaseViewOption $viewOption = null;
```
