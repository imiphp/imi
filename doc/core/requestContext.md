# 请求上下文

[toc]

当前请求的上下文，请求结束时即销毁，可以操作容器或者存取自定义值。

## 上下文中容器操作

```php
$object = \Imi\RequestContext::getBean('XXX');
$object = \Imi\RequestContext::getServerBean('XXX');
$object = \Imi\RequestContext::newInstance('XXX');

$container = \Imi\RequestContext::getContainer();
$object = $container->get('XXX');
```

## 上下文中的数据存储

### 获取上下文对象

```php
// @var ArrayObject $cxt
$cxt = \Imi\RequestContext::getContext();
```

### 在上下文中存取值

```php
\Imi\RequestContext::get('myKey', 'default');
\Imi\RequestContext::set('myKey', '123456');
\Imi\RequestContext::unset('myKey');
```

### 批量在上下文中设置值

```php
\Imi\RequestContext::muiltiSet([
    'myKey1' => '123456',
    'myKey2' => '456789',
    'myKey3' => '123456',
]);
```

### 在闭包中获取上下文操作并返回值

```php
// 返回`123`
$result = \Imi\RequestContext::use(function (ArrayObject $cxt) {
    $cxt['myKey2'] = '789';
    unset($cxt['myKey3']);

    return '123';
});
```

### 执行一个闭包并再上下文中记住其返回值

```php
// 返回值`3`并写入上下文中存储
$result = \Imi\RequestContext::remember('myKey3', function () {
    return 1 + 2;
});
```
