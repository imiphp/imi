# 全局函数

### imigo

启动一个协程，自动创建和销毁上下文

```php
imigo(function(){
    $value = Redis::get('key');
    // 无需手动释放任何资源
});
```

### imiCallable

为传入的回调自动创建和销毁上下文，并返回新的回调

```php
$callable = imiCallable(function(){
    $value = Redis::get('key');
    // 无需手动释放任何资源
});
function test($a)
{
    $a();
}
test($callable);
```
