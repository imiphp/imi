# ObjectArrayHelper

**类名:** `Imi\Util\ObjectArrayHelper`

对象及数组帮助类

智能识别数组和对象，支持对a.b.c这样的name属性进行操作

## 方法

以下所有示例，前提代码为：

```php
$data = [
    'a' =>  [
        'b' =>  [
            'c' =>  '111',
        ],
	],
];
// 也可以是任意对象及数组的混合用法，如：
$data = new \stdClass;
$data->a = [];
$data->a['b'] = new \stdClass;
$data->a['b']->c = '111';
```

### get

获取值

```php
// 111
echo ObjectArrayHelper::get($data, 'a.b.c');

// 222
echo ObjectArrayHelper::get($data, 'a.b.c.d', '222');
```

### set

设置值

```php
ObjectArrayHelper::set($data, 'a.b.c2', '333');
```

### remove

移除值

```php
ObjectArrayHelper::remove($data, 'a.b.c2');
```

### exists

值是否存在

```php
var_dump(ObjectArrayHelper::exists($data, 'a.b.c2'));
```

### filter

过滤属性

```php
$data = [
	'id'	=>	1,
	'name'	=>	'imi',
];
// $data = new stdClass;
// $data->id = 1;
// $data->name = 'imi';

// 以上两个都支持

// 只保留 name 字段
var_dump(ObjectArrayHelper::filter($data, ['name']));

// 剔除 name 字段
var_dump(ObjectArrayHelper::filter($data, ['name'], 'deny'));
```
