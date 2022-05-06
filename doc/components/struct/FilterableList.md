# FilterableList

[toc]

过滤字段的列表，每一个成员应该是数组或对象

```php
$data = [
    ['id'=>1, 'name'=>'a'],
    ['id'=>2, 'name'=>'b'],
];

// 两个毫无意义的实例化写法
$list = new FilterableList;
$list = new FilterableList($data);
// 只保留 name 字段
$list = new FilterableList($data, ['name']);
$list = new FilterableList($data, ['name'], 'allow');
// 剔除 name 字段
$list = new FilterableList($data, ['name'], 'deny');

foreach($list as $index => $item)
{
    var_dump($index, $item);
}

// 取其中某个成员
$item0 = $list[0];

// 转为数组
$arrayList = $list->toArray();
var_dump($arrayList);

// 移除多个成员
$arrayList->remove($list[1], $list[2]);

// 清空
$arrayList->clear();

// 统计数量
echo $arrayList->count();
```