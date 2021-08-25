# imi-rate-limit

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-rate-limit.svg)](https://packagist.org/packages/imiphp/imi-rate-limit)
[![Php Version](https://img.shields.io/badge/php-%3E=7.4-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.1.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-rate-limit.svg)](https://github.com/imiphp/imi-rate-limit/blob/master/LICENSE)

## 介绍

`imi-rate-limit` 是 `imi` 框架的限流组件，基于 `bandwidth-throttle/token-bucket` 开发。

本组件仅支持使用 `Redis` 作为中间件，可以针对方法、接口设置限流，通过设置`总容量、单位时间内生成填充的数量、每次扣除数量`实现限流。

> 本仓库仅用于浏览，不接受 issue 和 Pull Requests，请前往：<https://github.com/imiphp/imi>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-rate-limit": "~2.0.0"
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
        'RateLimit'    =>  'Imi\RateLimit',
    ],
    'pools'    =>    [
        // 一定得要配置 Redis 连接池才可以用
    ],
    'redis' =>  [
        'defaultPool'   =>  'redis连接池名称',
    ],
]
```

使用：

```php
/**
 * 限制每秒同时访问 3 次
 * 
 * @Action
 * 
 * @RateLimit(name="test1", capacity=3)
 *
 * @return void
 */
public function test1()
{
    return [
        'data'  =>  'test1',
    ];
}

/**
 * 限制每秒同时访问 1 次，等待解除限制后继续执行，超时时间为 1 秒
 * 
 * @Action
 * 
 * @RateLimit(name="test2", capacity=1)
 * @BlockingConsumer(1)
 *
 * @return void
 */
public function test2()
{
    return [
        'data'  =>  'test2',
    ];
}

/**
 * 总容量为 1000，每毫秒填充 1，每次调用扣除 500
 * 
 * 自定义处理限制
 * 
 * @Action
 * 
 * @RateLimit(name="test3", capacity=1000, fill=1, unit="millisecond", deduct=500, callback="\ImiDemo\HttpDemo\Util\RateLimitParser::parse")
 *
 * @return void
 */
public function test3()
{
    return [
        'data'  =>  'test3',
    ];
}

/**
 * 手动调用限流
 * 
 * 总容量为 1000，每毫秒填充 1，每次调用扣除 500
 *
 * @Action
 * 
 * @return void
 */
public function test4()
{
    if(true !== $result = RateLimiter::limit('test4', 1000, function(){
        // 自定义回调中的返回值，会作为原方法的返回值被返回
        return [
            'message'   =>  '自定义触发限流返回内容',
        ];
    }, 1, 'millisecond', 500))
    {
        return $result;
    }
    return [
        'data'  =>  'test4',
    ];
}

/**
 * 手动调用限流
 * 
 * 限制每秒同时访问 1 次，等待解除限制后继续执行，超时时间为 1 秒
 *
 * @Action
 * 
 * @return void
 */
public function test5()
{
    if(true !== $result = RateLimiter::limitBlock('test5', 1, function(){
        // 自定义回调中的返回值，会作为原方法的返回值被返回
        return [
            'message'   =>  '自定义触发限流返回内容',
        ];
    }, 1, 1, 'second', 1))
    {
        return $result;
    }
    return [
        'data'  =>  'test5',
    ];
}
```

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.4
- [Composer](https://getcomposer.org/) >= 2.0
- [Swoole](https://www.swoole.com/) >= 4.1.0

## 版权信息

`imi-rate-limit` 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://raw.githubusercontent.com/imiphp/imi/2.0/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
