# 定时器

imi 定时器提供了定时执行和延后执行功能，都是异步支持的。

类名：`\Imi\Timer\Timer`

## 环境支持

名称 | 是否支持 | 备注
-|-|-
php-fpm | × | 为了保证兼容性，php-fpm 中使用了定时器的话，会立即执行回调。 |
swoole | √ |  |
workerman | √ |  |

## 调用方法

### 无限执行

```php
// 每隔 1 秒执行一次
$timerId = Timer::tick(1000, function(){
    // 执行内容
});
```

### 只执行一次

```php
// 1 秒后执行一次
$timerId = Timer::after(1000, function(){
    // 执行内容
});
```

### 删除定时器

```php
Timer::del($timerId);
```

### 清空所有定时器

```php
Timer::clear();
```
