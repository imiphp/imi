# Swagger API 文档生成

[toc]

支持在项目中使用 Swagger 注解语法，运行命令，生成 Swagger 文件。

Swagger 是最流行的 API 开发工具，它遵循 OpenAPI Specification（OpenAPI 规范，也简称 OAS）。

Swagger 可以贯穿于整个 API 生态，如 API 的设计、编写 API 文档、测试和部署。

Swagger 是一种通用的，和编程语言无关的 API 描述规范。

imi-apidoc 基于 [zircote/swagger-php](https://github.com/zircote/swagger-php) 开发，100% 支持写法。

Github: <https://github.com/imiphp/imi-apidoc>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-apidoc": "~2.1.0"
    }
}
```

然后执行 `composer update` 安装。

## 使用说明

> 可以参考 `example`、`tests` 目录示例。

项目配置文件：

```php
[
    'components'    =>  [
        'ApiDoc'  =>  'Imi\ApiDoc',
    ],
]
```

Swagger 书写文档说明：<https://zircote.github.io/swagger-php/Getting-started.html#annotation-placement>

**Demo:**

```php
<?php
namespace ImiApp\ApiServer\Controller;

use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Controller\SingletonHttpController;
use Imi\Server\Http\Route\Annotation\Controller;

/**
 * @OA\Info(title="My First API", version="0.1")
 * @Controller("/")
 */
class IndexController extends SingletonHttpController
{
    /**
     * @Action
     * @Route("/")
     * 
     *
     * @return void
     */
    public function index()
    {

    }

    /**
     * @Action
     * @Route(url="login", method="POST")
     *
     * @param string $username 用户名
     * @param integer $password 密码
     * 
     * @return void
     */
    public function login(string $username, int $password)
    {

    }

    /**
     * @Action
     * @Route("register")
     * @OA\Get(
     *     path="/register",
     *     @OA\Response(response="200", description="An example resource")
     * )
     *
     * @param string $username 用户名
     * @param integer $password 密码
     * @param string $birthday 生日
     * 
     * @return void
     */
    public function register(string $username, int $password
    , string $birthday)
    {

    }

    /**
     * @Action
     *
     * @param int $id
     * @return void
     */
    public function get(int $id)
    {

    }

}
```

imi-apidoc 会根据 `@Route` 注解、`@param` 注释，自动补足相关信息。让你不必为每个接口都书写 Swagger 注解，提升开发效率。

当然，如果希望更加个性化的信息设置，还是要自己去书写的！

**生成命令：**

Yaml 格式: `imi doc/api -to api.yml`

Json 格式: `imi doc/api -to api.json`

指定扫描的命名空间：`imi doc/api -to api.json -namespace "ImiApp\Controller1,ImiApp\Controller2"`

**效果：**

![avatar](../../res/1.jpg)

![avatar](../../res/2.jpg)
