# 第三方支付 SDK

[PaySDK](https://github.com/Yurunsoft/PaySDK) 是 PHP 集成支付 SDK ，集成了支付宝、微信支付的支付接口和其它相关接口的操作。可以轻松嵌入支持 PHP >= 5.4 的任何系统中，2.0 版现已支持 Swoole 协程环境。

## 支持的支付接口

### 支付宝

* 即时到账-电脑网站支付（老）
* 即时到账-手机网站支付（老）
* 当面付
* 手机网站支付
* 电脑网站支付
* APP支付服务端
* 单笔转账到支付宝账户
* 海外支付（电脑网站、手机网站、APP、扫码）
* 海关报关
* 其它辅助交易接口（退款、查询等）

### 微信支付

* 刷卡支付
* 公众号支付
* 扫码支付
* APP支付
* H5支付
* 小程序支付
* 企业付款到零钱
* 企业付款到银行卡
* 海外支付（刷卡、公众号、扫码、APP）
* 海关报关
* 其它辅助交易接口（退款、查询等）

## 安装

在您的composer.json中加入配置：

```json
{
    "require": {
        "yurunsoft/pay-sdk": "~2.1"
    }
}
```

然后执行`composer update`命令。


### Swoole 协程环境支持

在 `WorkerStart` 事件中加入：

```php
\Yurun\Util\YurunHttp::setDefaultHandler('Yurun\Util\YurunHttp\Handler\Swoole');
```

在支付、退款异步通知中，需要赋值 `Swoole` 的 `Request` 和 `Response` 对象，或者遵循 PSR-7 标准的对象即可。

#### imi 框架中使用

imi 是基于 PHP Swoole 的高性能协程应用开发框架，它支持 HttpApi、WebSocket、TCP、UDP 服务的开发。

在 Swoole 的加持下，相比 php-fpm 请求响应能力，I/O密集型场景处理能力，有着本质上的提升。

imi 框架拥有丰富的功能组件，可以广泛应用于互联网、移动通信、企业软件、云计算、网络游戏、物联网（IOT）、车联网、智能家居等领域。可以使企业 IT 研发团队的效率大大提升，更加专注于开发创新产品。

<https://www.imiphp.com/>

```php
/**
 * 这是一个在控制器中的动作方法
 * @Action
 */
public function test()
{
    $payNotify = new class extends \Yurun\PaySDK\Weixin\Notify\Pay
    {
        /**
         * 后续执行操作
         * @return void
         */
        protected function __exec()
        {

        }
    };
    $context = RequestContext::getContext();
    // 下面两行很关键
    $payNotify->swooleRequest = $context['request'];
    $payNotify->swooleResponse = $context['response'];

    $sdk->notify($payNotify);

    // 这句话必须填写
    return $payNotify->swooleResponse;
}
```
