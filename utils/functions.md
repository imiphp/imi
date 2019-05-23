# 全局函数

### imigo

启动一个协程，自动创建和销毁上下文

```php
imigo(function(){
    $value = Redis::get('key');
    // 无需手动释放任何资源
});
```

传入参数：

```php
imigo(function($id, $name){
    echo $id, ':', $name, PHP_EOL; // 1:test
}, 1, 'test');
```

### imiCallable

为传入的回调自动创建和销毁上下文，并返回新的回调

```php
$callable = imiCallable(function(){
    return 'abc';
});
function test($a)
{
    $value = $a(); // abc
}
test($callable);
```

开新协程去执行回调，无法获取返回值：

```php
$callable = imiCallable(function(){
    return 'abc';
}, true); // 这里加了 true
function test($a)
{
    $value = $a(); // 协程ID
}
test($callable);
```