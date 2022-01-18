# imi-jwt

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-jwt.svg)](https://packagist.org/packages/imiphp/imi-jwt)
[![Php Version](https://img.shields.io/badge/php-%3E=7.4-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.1.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-jwt.svg)](https://github.com/imiphp/imi-jwt/blob/master/LICENSE)

## 介绍

在 imi 框架中非常方便地接入 jwt

> 本仓库仅用于浏览，不接受 issue 和 Pull Requests，请前往：<https://github.com/imiphp/imi>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-jwt": "~2.1.0"
    }
}
```

然后执行 `composer update` 安装。

## 使用

在项目 `config/config.php` 中配置：

```php
[
    'components'    =>  [
        // 引入本组件
        'jwt'    =>  'Imi\JWT',
    ],
]
```

### 配置

配置 `@app.beans`：

```php
[
    'JWT'   =>  [
        'list'  =>  [
            // a 为名称，可以自定义，以下被注释的项为非必设，一般有默认值
            'a' =>  [
                // 'signer'    =>  'Hmac',      // 签名者，可选：Ecdsa/Hmac/Rsa
                // 'algo'      =>  'Sha256',    // 算法，可选：Sha256/Sha384/Sha512
                // 'dataName'  =>  'data',      // 自定义数据字段名，放你需要往token里丢的数据
                // 'audience'  =>  null,        // 接收，非必须
                // 'subject'   =>  null,        // 主题，非必须
                // 'expires'   =>  null,        // 超时秒数，非必须
                // 'issuer'    =>  null,        // 发行人，非必须
                // 'notBefore' =>  null,        // 实际日期必须大于等于本值
                // 'issuedAt'  =>  true,        // JWT 发出时间。设为 true 则为当前时间；设为 false 不设置；其它值则直接写入
                // 'id'        =>  null,        // Token id
                // 'headers'   =>  [],          // 头
                // 自定义获取 token 回调，返回值为 Token。默认从 Header Authorization 中获取。
                // 'tokenHandler'  =>  null,
                'privateKey'    =>  '123456',// 私钥
                'publicKey'     =>  '123456',// 公钥
            ],
        ],
    ],
]
```

### 生成 Token

简单生成：

```php
use \Imi\JWT\Facade\JWT;
// 你需要往token里丢的数据
$data = [
    'memberId'  =>  19260817,
];
$token = JWT::getToken($data); // Token 对象
$tokenContent = $token->__toString(); // Token 字符串
```

指定名称：

```php
use \Imi\JWT\Facade\JWT;
// 你需要往token里丢的数据
$data = [
    'memberId'  =>  19260817,
];
$token = JWT::getToken($data, 'a'); // Token 对象
$tokenContent = $token->__toString(); // Token 字符串
```

自定义处理：

```php
use \Imi\JWT\Facade\JWT;
// 你需要往token里丢的数据
$data = [
    'memberId'  =>  19260817,
];
$token = JWT::getToken($data, 'a', function(\Lcobucci\JWT\Builder $builder){
    // 可以针对该对象做一些操作
    $builder->withClaim('aaa', 'bbb');
}); // Token 对象
$tokenContent = $token->__toString(); // Token 字符串
```

### 验证 Token

手动验证：

```php
use \Imi\JWT\Facade\JWT;
/** @var \Lcobucci\JWT\Token $token */
$token = JWT::parseToken($jwt); // 仅验证是否合法
// $token = JWT::parseToken($jwt, 'a'); // 指定配置名称
$data = $token->getClaim('data'); // 获取往token里丢的数据

// 验证有效期、id、issuer、audience、subject
$validationData = new \Lcobucci\JWT\ValidationData;
$validationData->setId('');
$validationData->setIssuer('');
$validationData->setAudience('');
$validationData->setSubject('');
if($token->validate($validationData))
{
    // 合法
}
else
{
    // 不合法
}
```

注解验证：

```php
<?php
namespace Imi\JWT\Test\Test;

use Imi\Bean\Annotation\Bean;
use Imi\JWT\Annotation\JWTValidation;

/**
 * @Bean("A")
 */
class A
{
    /**
     * @JWTValidation(tokenParam="token", dataParam="data")
     *
     * @param \Lcobucci\JWT\Token $token
     * @param \stdClass $data
     * @return array
     */
    public function test($token = null, $data = null)
    {
        return [$token, $data];
    }

}
```

**@JWTValidation**

JWT 验证注解

| 属性名称 | 说明 |
|-|-
| name | JWT 配置名称 |
| id | 验证 ID。为 `null` 则使用配置中的值验证；为 `false` 则不验证 |
| issuer | 验证发行人。为 `null` 则使用配置中的值验证；为 `false` 则不验证 |
| audience | 验证接收。为 `null` 则使用配置中的值验证；为 `false` 则不验证 |
| subject | 验证主题。为 `null` 则使用配置中的值验证；为 `false` 则不验证 |
| tokenParam | Token 对象注入的参数名称 |
| dataParam | 数据注入的参数名称 |

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.4
- [Composer](https://getcomposer.org/) >= 2.0
- [Swoole](https://www.swoole.com/) >= 4.1.0

## 版权信息

`imi-jwt` 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://raw.githubusercontent.com/imiphp/imi/2.1/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
