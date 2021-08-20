# imi-apidoc

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-apidoc.svg)](https://packagist.org/packages/imiphp/imi-apidoc)
[![Php Version](https://img.shields.io/badge/php-%3E=7.4-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.7.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-apidoc.svg)](https://github.com/imiphp/imi-apidoc/blob/master/LICENSE)

## 介绍

支持在项目中使用 Swagger 注解语法，运行命令，生成 Swagger 文件。

Swagger 是最流行的 API 开发工具，它遵循 OpenAPI Specification（OpenAPI 规范，也简称 OAS）。

Swagger 可以贯穿于整个 API 生态，如 API 的设计、编写 API 文档、测试和部署。

Swagger 是一种通用的，和编程语言无关的 API 描述规范。

imi-apidoc 基于 [zircote/swagger-php](https://github.com/zircote/swagger-php) 开发，100% 支持写法。

> 本仓库仅用于浏览，不接受 issue 和 Pull Requests，请前往：<https://github.com/imiphp/imi>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-apidoc": "~2.0.0"
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

use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Controller\SingletonHttpController;
use Imi\Server\Route\Annotation\Controller;

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

<img src="https://raw.githubusercontent.com/imiphp/imi-apidoc/master/res/1.jpg"/>

<img src="https://raw.githubusercontent.com/imiphp/imi-apidoc/master/res/2.jpg"/>

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.4
- [Composer](https://getcomposer.org/) >= 2.0
- [Swoole](https://www.swoole.com/) >= 4.7.0

## 版权信息

`imi-apidoc` 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://cdn.jsdelivr.net/gh/imiphp/imi@2.0/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
