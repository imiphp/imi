# 控制器

[toc]

所有的请求都会打进控制器，我们开发项目时候，处理请求基本都是在控制器里做。

如下代码所示，一个最简单的控制器代码。

## 基本控制器

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

```text
hello imi!
```

### Swoole 中的禁忌用法

控制器是单例的，Swoole 环境下运行不适合赋值取值静态变量、类属性。

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
    private $id;

    public function __construct()
    {
        // 这个是有问题的，只会在第一次请求时候执行
        $this->id = $this->request->get('id');
    }
}
```

## 绑定请求参数到控制器方法参数

详见：[链接](/v3.0/components/httpserver/request.html#%E7%BB%91%E5%AE%9A%E8%AF%B7%E6%B1%82%E5%8F%82%E6%95%B0%E5%88%B0%E6%8E%A7%E5%88%B6%E5%99%A8%E6%96%B9%E6%B3%95%E5%8F%82%E6%95%B0)

## 属性

### $server

详见：[链接](/v3.0/core/server.html)

### $request

请求信息对象，可以用于获取参数、请求头等，遵循 PSR-7 标准。

### $response

响应对象，遵循 PSR-7 标准。

直接对该对象调用 `withXXX()` 无效，需要如下使用才可。

1. 操作后赋值：

    ```php
    public function action()
    {
        $this->response = $this->response->withStatus(404);
    }
    ```

2. 操作后返回：

    ```php
    public function action()
    {
        return $this->response->withStatus(404);
    }
    ```

或者可以直接使用 `setXXX()`：

```php
public function action()
{
    $this->response->setStatus(404);
}
```
