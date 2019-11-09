# ArrayList

限定成员类型的数组列表

如果成员类型不正确，会抛出`\InvalidArgumentException`异常

```php
class TestModel
{
    public $id;

    public $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}

// $list = new \Imi\Util\ArrayList(TestModel::class);
$list = new \Imi\Util\ArrayList(TestModel::class, [
    new TestModel(1, 'a'),
    new TestModel(2, 'b'),
]);

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
