# ArrayData

[toc]

数组数据基类，支持使用 `a.b.c` 方式操作数据

```php
$data = [
    'a' =>  [
        'b' =>  [
            'c' =>  [
                'name'  =>  'imi',
            ]
        ]
    ]
];
$data = new \Imi\Util\ArrayData($data);
// 3 种不同操作方法，输出都是 imi
var_dump($data->get('a.b.c.name'));
var_dump($data['a.b.c.name']);
var_dump($data->{'a.b.c.name'});

$data->set('a.id', 123); // a.id 直接作为key，而不是多级数组
var_dump($data['a.id']); // false
var_dump($data->get()['a.id']); // 123

$data->setVal('a.id', 456);
var_dump($data['a.id']); // 456

// 第 3 个参数不合并，设置过后，a.b.c.name 将不存在
$data->set([
    'a' => [
        'b' => [
            'c' => [
                'noname' => true,
            ],
        ],
    ],
], null, false);

$data->clear(); // 全部清除
```