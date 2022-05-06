# 第三方授权 SDK

[toc]

[YurunOAuthLogin](https://github.com/Yurunsoft/YurunOAuthLogin) 是一个PHP 第三方登录授权 SDK，集成了QQ、微信、微博、Github等常用接口。可以轻松嵌入支持 PHP >= 5.4 的任何系统中，2.0 版现已支持 Swoole 协程环境。

## 支持的登录平台

- QQ、QQ 小程序
- 微信网页扫码、微信公众号、微信小程序
- 微博
- 百度
- Github
- Gitee
- Coding
- 开源中国(OSChina)
- CSDN

> 后续将不断添加新的平台支持，也欢迎你来提交PR，一起完善！

## 安装

在您的composer.json中加入配置：

```json
{
    "require": {
        "yurunsoft/yurun-oauth-login": "~2.0"
    }
}
```

## 代码实例

自v1.2起所有方法统一参数调用，如果需要额外参数的可使用对象属性赋值，具体参考test目录下的测试代码。

下面代码以QQ接口举例，完全可以把QQ字样改为其它任意接口字样使用。

### 实例化

```php
$qqOAuth = new \Yurun\OAuthLogin\QQ\OAuth2('appid', 'appkey', 'callbackUrl');
```

### 登录

```php
$url = $qqOAuth->getAuthUrl();
$_SESSION['YURUN_QQ_STATE'] = $qqOAuth->state;
header('location:' . $url);
```

### 回调处理

```php
// 获取accessToken
$accessToken = $qqOAuth->getAccessToken($_SESSION['YURUN_QQ_STATE']);

// 调用过getAccessToken方法后也可这么获取
// $accessToken = $qqOAuth->accessToken;
// 这是getAccessToken的api请求返回结果
// $result = $qqOAuth->result;

// 用户资料
$userInfo = $qqOAuth->getUserInfo();

// 这是getAccessToken的api请求返回结果
// $result = $qqOAuth->result;

// 用户唯一标识
$openid = $qqOAuth->openid;
```

### 解决第三方登录只能设置一个回调域名的问题

```php
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考test/QQ/loginAgent.php写法
$qqOAuth->loginAgentUrl = 'http://localhost/test/QQ/loginAgent.php';

$url = $qqOAuth->getAuthUrl();
$_SESSION['YURUN_QQ_STATE'] = $qqOAuth->state;
header('location:' . $url);
```

### Swoole 协程环境支持

```php
\Yurun\Util\YurunHttp::setDefaultHandler('Yurun\Util\YurunHttp\Handler\Swoole');
```
