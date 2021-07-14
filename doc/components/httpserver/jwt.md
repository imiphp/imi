# JWT

## 介绍

在 imi 框架中非常方便地接入 jwt

Github: <https://github.com/imiphp/imi-jwt>

imi v1 使用 1.0 版本

imi v2 使用 2.0 版本

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-jwt": "~1.0"
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
                'expires'   =>  86400,        // 超时秒数
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
$tokenContent = $token->toString(); // Token 字符串
```

指定名称：

```php
use \Imi\JWT\Facade\JWT;
// 你需要往token里丢的数据
$data = [
    'memberId'  =>  19260817,
];
$token = JWT::getToken($data, 'a'); // Token 对象
$tokenContent = $token->toString(); // Token 字符串
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
$tokenContent = $token->toString(); // Token 字符串
```

### 验证 Token

手动验证：

```php
use \Imi\JWT\Facade\JWT;
/** @var \Lcobucci\JWT\Token $token */
$token = JWT::parseToken($jwt); // 仅验证是否合法
// $token = JWT::parseToken($jwt, 'a'); // 指定配置名称
$data = $token->getClaim('data'); // 获取往token里丢的数据，PHP <= 7.3
$data = $token->claim()->get('data'); // 获取往token里丢的数据，PHP >= 7.4

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

### Rsa配置
配置 `@app.beans`：
- 打开Git Bash Here 用openssl生成私匙文件
- openssl genrsa -out pri_key.pem 1024
- 根据私匙文件生成公匙
- openssl rsa -in pri_key.pem -pubout -out pub_key.pem
- 复制文件内容到配置上

```php
[
    'JWT'   =>  [
        'list'  =>  [
            // a 为名称，可以自定义，以下被注释的项为非必设，一般有默认值
            'b' =>  [
                'signer'    =>  'Rsa',      // 签名者，可选：Ecdsa/Hmac/Rsa
                'algo'      =>  'Sha256',    // 算法，可选：Sha256/Sha384/Sha512
                // 'dataName'  =>  'data',      // 自定义数据字段名，放你需要往token里丢的数据
                // 'audience'  =>  null,        // 接收，非必须
                // 'subject'   =>  null,        // 主题，非必须
                'expires'   =>  86400,        // 超时秒数
                // 'issuer'    =>  null,        // 发行人，非必须
                // 'notBefore' =>  null,        // 实际日期必须大于等于本值
                // 'issuedAt'  =>  true,        // JWT 发出时间。设为 true 则为当前时间；设为 false 不设置；其它值则直接写入
                // 'id'        =>  null,        // Token id
                // 'headers'   =>  [],          // 头
                // 自定义获取 token 回调，返回值为 Token。默认从 Header Authorization 中获取。
                // 'tokenHandler'  =>  null,
                'privateKey'    =>  '-----BEGIN RSA PRIVATE KEY-----
MIICXwIBAAKBgQDXj+GqRbpV2n2H1iOdnXwtvv6K+W7g9VqCQv7cf/DobqOH8cA7
8jjsujk51ovklZl5q7NR/sr4se9ghYH3QecLKKeMZeSQh+4LJubdkAbv+0Wmig/Y
iAGTtJrK55OfVoVAJeCrMLERWPHvpnLBF/VoCjy0PwuExVEpA6dwqiD0xQIDAQAB
AoGBALeTLNdZMmrS+3ym/QXJjGtY8GViLu8dg8rTS0B1JLCNKG8pjlB+48OWhA2h
jNlKHb3kX35AwpIw1m8Yw6nSUfM6uN0rL86IaHx6OzpB5ltWodXmHISATYcZ9Xn7
5+dKRqpGPW5QR7N8lPrHGRN69o74bT4X7DVyZzsdfjgOna0xAkEA7GVmUjVFUg7B
8KsypQKwDXF5kMNoC3CSjdQTBgSYXNjo1kQjBGoDgPlcEhbV0Ishw4z7sGdxnvkc
id4KbnkwrwJBAOlwLsFDn7eXmVcmRb7s2enXkUdfp8gXzg57VFcxZ5UML1P6vilH
F4LpJjqtluZGbvA7oD3dkaks7POkt0mgxssCQQDaB2fI+KL33O6Y530tXf48V+WU
U/W5X1l8ABaPnVtdfx24yV02rASRRuvZL0CDOF+quXRFrhLIWeAtdCJQ4+u3AkEA
gVHGdQZTas+vARqQtM5dgjALqXCScETPwDIObSdPbMCNT4au5gseOUWUChm0aOlH
+AnwIZWnZgMfWXI8n6tTtQJBAKGVtbVsmwZN+5UDLziwWR6VLcZCzymocbJZ/PGi
e49lnvOMZXg+9uLaQBiKCUqftQXaaFKj06hox6+25Rw1T0g=
-----END RSA PRIVATE KEY-----
',// 私钥
                'publicKey'     =>  '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXj+GqRbpV2n2H1iOdnXwtvv6K
+W7g9VqCQv7cf/DobqOH8cA78jjsujk51ovklZl5q7NR/sr4se9ghYH3QecLKKeM
ZeSQh+4LJubdkAbv+0Wmig/YiAGTtJrK55OfVoVAJeCrMLERWPHvpnLBF/VoCjy0
PwuExVEpA6dwqiD0xQIDAQAB
-----END PUBLIC KEY-----',// 公钥
            ],
        ],
    ],
]
```