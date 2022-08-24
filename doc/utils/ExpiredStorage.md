# ExpiredStorage

[toc]

**类名:** `Imi\Util\ExpiredStorage`

支持键值过期的存储对象

## 方法

### __construct

```php
$storage = new \Imi\Util\ExpiredStorage();
$storage = new \Imi\Util\ExpiredStorage([
    'key' => 'value',
]);
```

### set

设置值

```php
$storage->set('key', 'value');
$ttl = 1.5; // 过期时间，单位：秒
$storage->set('key', 'value', $ttl);
```

### get

获取值，不会返回已过期的值

```php
var_dump($storage->get('key'));
var_dump($storage->get('key', 'default')); // 缺省默认值

// 获取存储对象 $item
var_dump($storage->get('key', null, $item));
$item->setValue('value'); // 设置值
$item->getValue(); // 获取值，不受超时时间限制
$item->setTTL(1.5); // 设置超时时间
$item->getTTL(); // 获取超时时间
$item->isExpired(); // 是否过期
$item->getLastModifyTime(); // 获取数据最后修改时间，microtime(true) 的返回值，小数，单位：秒
```

### unset

删除值

```php
$storage->unset('key');
```

### isset

值是否存在，过期会返回 `false`

```php
$storage->isset('key');
```

### clear

清空

```php
$storage->clear();
```

### 获取所有存储对象

```php
$items = $storage->getItems();
```
