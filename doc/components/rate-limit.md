# 限流

## 介绍

`imi-rate-limit` 是 `imi` 框架的限流组件，基于 `bandwidth-throttle/token-bucket` 开发。

本组件仅支持使用 `Redis` 作为中间件，可以针对方法、接口设置限流，通过设置`总容量、单位时间内生成填充的数量、每次扣除数量`实现限流。

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

阿里云集群用户，请把 `script_check_enable` 设置改为 `0`

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
