# ArrayUtil

**类名:** `Imi\Util\ArrayUtil`

数组帮助类

## 方法

### remove

从数组中移除一个元素

```php
$array = [1, 2, 3, 4, 5];

// 删除一个元素：[1, 3, 4, 5]
var_dump(ArrayUtil::remove($array, 2));

// 删除多个元素：[1, 5]
var_dump(ArrayUtil::remove($array, 2, 3, 4));
```

### recursiveMerge

多维数组递归合并，具体效果请看下面代码及运行结果

```php
$array1 = [
	'a'	=>	[
		'b1'	=>	[
			'c1'	=>	1,
		],
		'b2'	=>	[
			'c2'	=>	2,
		]
	]
];
$array2 = [
	'a'	=>	[
		'b1'	=>	[
			'c1'	=>	3,
		]
	]
];

// array_merge
print_r(array_merge($array1, $array2));

// +
print_r($array1 + $array2);

// ArrayUtil::recursiveMerge
print_r(ArrayUtil::recursiveMerge($array1, $array2));
```

运行结果：

```php
Array
(
    [a] => Array
        (
            [b1] => Array
                (
                    [c1] => 3
                )
        )
)
Array
(
    [a] => Array
        (
            [b1] => Array
                (
                    [c1] => 1
                )
            [b2] => Array
                (
                    [c2] => 2
                )
        )
)
Array
(
    [a] => Array
        (
            [b1] => Array
                (
                    [c1] => 3
                )
            [b2] => Array
                (
                    [c2] => 2
                )
        )
)
```

### columnToKey

将二维数组第二纬某key变为一维的key

```php
$array = [
	['id'=>1,'name'=>'a'],
	['id'=>2,'name'=>'b'],
	['id'=>3,'name'=>'c'],
];

// 保留原始字段
print_r(ArrayUtil::columnToKey($array, 'id'));

// 去除原始字段
print_r(ArrayUtil::columnToKey($array, 'id', false));
```

运行结果：

```php
Array
(
    [1] => Array
        (
            [id] => 1
            [name] => a
        )
    [2] => Array
        (
            [id] => 2
            [name] => b
        )
    [3] => Array
        (
            [id] => 3
            [name] => c
        )

)
Array
(
    [1] => Array
        (
            [name] => a
        )
    [2] => Array
        (
            [name] => b
        )
    [3] => Array
        (
            [name] => c
        )
)
```

### isAssoc

判断数组是否为关联数组

```php
$array = [1, 2, 3];
// false
var_dump(ArrayUtil::isAssoc($array));

$array = [1, 'b'=>2, 3];
// true
var_dump(ArrayUtil::isAssoc($array));
```