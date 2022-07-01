# 模型用法

[toc]

## 模型常量

`TestModel::PRIMARY_KEY` 第一个主键名称，字符串

`TestModel::PRIMARY_KEYS` 主键名称数组

## 模型操作

### 初始化模型

```php
$testModel = TestModel::newInstance([
    'a'	=>	'abc',
    'b'	=>	'def',
]);
```

第二种方法：

```php
$testModel = TestModel::newInstance();
$testModel->set([
    'a'	=>	'abc',
    'b'	=>	'def',
]);
```

### 插入

```php
$testModel = TestModel::newInstance();
$testModel->setA('1');
$testModel->setB('1');
$testModel->setC('1');
$testModel->__setRaw('value', 'value+1'); // set value=value+1，第一个参数是字段名，第二个参数是sql
$result = $testModel->insert();
// $result 用法同数据库中的 insert() 返回值用法
echo '插入的自增ID：', $testModel->getId();
```

> 未设置值的字段（以注解定义为准），默认以 `null` 值插入，如有需要建议给字段设置默认值

### 更新

```php
$testModel = TestModel::find(1, 'abc');
$result = $testModel->update();
// $result 用法同数据库中的 update() 返回值用法
```

### 保存

```php
// 自动判断是插入还是更新
$testModel->save();
```

### 删除

```php
$testModel = TestModel::find(1, 'abc');
$result = $testModel->delete();
// $result 用法同数据库中的 delete() 返回值用法
```

## 查询构建器

imi 中数据库查询连贯操作都来自于查询器，查询器的创建方式：

**查询结果返回模型对象：**

```php
$query = TestModel::query();
```

> `query()` 方法返回的类是 `Imi\Model\Contract\IModelQuery`，它继承了 `Imi\Db\Query\Interfaces\IQuery`，并且有扩展特性

**查询结果返回数组：**

```php
$query = TestModel::dbQuery();
```

> `dbQuery()` 方法返回的类是 `Imi\Db\Query\Interfaces\IQuery`，与 `Imi\Db\Db::query()` 返回完全一致

**IModelQuery 扩展特性：**

### 关联模型预加载

```php
$list = TestModel::query()
                ->with('关联字段名') // 单个
                ->with(['字段名1', '字段名2']) // 多个
                ->with([
                    // 回调第一个参数是：模型查询构建器
                    // 第二个参数是当前关联查询对应的注解对象，如果不确定什么类型可以写 RelationBase，如果确定类型也可以写具体类型，比如：\Imi\Model\Annotation\Relation\OneToOne
                    '字段名1' => function(\Imi\Model\Contract\IModelQuery $query, \Imi\Model\Annotation\Relation\RelationBase $annotation) {
                        $query->withField('a', 'b'); // 限定查询结果模型的可序列化字段
                    },
                ]) // 回调
                ->where('id', '=', 1)->select()->getArray();
```

> 使用预加载后，模型关联的前后置事件都不会触发

---

> 和 `withField()` 一起用时，`with()` 中查询的关联字段可以手动获取。但如果要在 JSON 序列化时返回，需要在 `withField()` 中指定该关联字段

```php
$list = TestModel::query()->with(['a'])->withField('b')->select()->getArray(); // JSON 序列化时，只返回字段 b，而没有 a
$list = TestModel::query()->with(['a'])->withField('a', 'b')->select()->getArray(); // JSON 序列化时，返回字段中有a、b
```

### 指定查询出的模型可序列化的字段

> 必须使用驼峰命名

```php
$list1 = TestModel::query()->withField('id', 'name')->select()->getArray();

// 上面的代码同下面的效果
$list2 = TestModel::query()->select()->getArray();
foreach ($list2 as $row)
{
    $list2->__setSerializedFields(['id', 'name']);
}
```

### 判断记录是否存在

```php
// where id = 1
$exists = TestModel::exists(1);
// 复合主键 where a = 1 and b = 'abc'
$exists = TestModel::exists(1, 'abc');
// 指定多个字段条件 where a = 1 and b = 'abc'
$exists = TestModel::exists([
    'a' => 1,
    'b' => 'abc',
]);
```

### 查询记录

查询记录并返回一个模型实例。

```php
// where id = 1
$testModel = TestModel::find(1);
// 复合主键 where a = 1 and b = 'abc'
$testModel = TestModel::find(1, 'abc');
echo $testModel->getId();
// 指定多个字段条件 where a = 1 and b = 'abc'
$testModel = TestModel::find([
    'a' => 1,
    'b' => 'abc',
]);
// 通过构建器查询
$testModel = TestModel::query()->where('id', '=', 1)->find();
```

#### 查询单个值

```php
TestModel::query()->value('username');
TestModel::query()->value('username', -1); // 当数据查询不到时返回的默认值 -1
```

#### 查询指定列

```php
// 查询 username 列并作为数组返回
TestModel::query()->column('username');
// 结果值如下：
[
    'username1',
    'username2',
    'username4',
]
// 查询 username 列并返回 id 做下标，username 作为值的数组
TestModel::query()->column('username', 'id');
// 结果值如下：
[
    1 => 'username1',
    2 => 'username2',
    3 => 'username4',
]

// 查询 username、password 列并返回 id 做下标的数组
TestModel::query()->column(['username', 'password'], 'id');
// 结果值如下：
[
    1 => ['username' => 'username1', 'password' => '123'],
    2 => ['username' => 'username2', 'password' => '456'],
    3 => ['username' => 'username3', 'password' => '789'],
]
```

### 批量查询记录

```php
// 查询所有记录
$list = TestModel::select();

// 带 where 条件的查询，id = 1
$list = TestModel::select([
    'id'	=>	1
]);

// where 回调条件
$list = TestModel::select(function(IQuery $query){
    $query->where('id', '=', 1);
});

// 查询器查询
$list = TestModel::query()->where('id', '=', 1)->select()->getArray();

// 以上所有 $list 都是 TestModel[] 类型
```

### 分页查询带扩展字段

**查询总记录数、总页数：**

```php
$page = 1;
$count = 10;
$data = TestModel::query()->paginate($page, $count);
// 指定转数组后的字段名
$data = TestModel::query()->paginate($page, $count, [
    'field_list' => 'list',
    'field_limit' => 'limit',
    'field_total' => 'total',
    'field_page_count' => 'page_count',
]);

$data->getList(); // 列表数据
$data->getTotal(); // 总记录数
$data->getLimit(); // $count === 10
$data->getPageCount(); // 总页数

var_dump($data->toArray()); // 转数组
var_dump(json_encode($data)); // 支持序列化
// 数据格式如下：
[
    'list'          => [],
    'total'         => 100,
    'limit'         => 10,
    'page_count'    => 10,
]
```

**不查询总记录数、总页数：**

```php
$page = 1;
$count = 10;
$data = TestModel::query()->paginate($page, $count, [
    'total' =>  false,
]);
var_dump($data->toArray()); // 转数组
var_dump(json_encode($data)); // 支持序列化
// 数据格式如下：
[
    'list'          => [],
    'limit'         => 10,
]
```

**全局设置转数组后的字段名：**

配置`@app.db.paginate.fields`:

```php
[
    'list' => 'list',
    'limit' => 'limit',
    'total' => 'total',
    'pageCount' => 'page_count',
]
```

#### 分块查询

##### chunkById

利用有序字段进行分段读取，返回符合条件的数据，对于大型数据集结果，可以有效缓解数据库压力，降低应用内存消耗，提升稳定性。

| 参数 | 类型 | 说明                                                        |
| ------ | ------ |-----------------------------------------------------------|
| limit  | int    | 每次查询的块大小                                                  |
| column | string | 用于分块的有序字段，建议是有序且存在索引的数值字段，一般情况下可以利用主键                     |
| alias  | string | 用于分块的有序字段的别名，一般情况下跟`column`是一致的，无须设置，如果定义字段查询结果的别名时可设置该参数 |

> 由于不是一次性载入全部数据，预加载功能对于每个块都是重复加载的，建议根据实际情况决定是否实现一个缓存查询来替代预加载。

```php
// 按 10 条每块遍历全部符合条件的行。

foreach (TestModel::query()->chunkById(10, 'id') as $result)
{
    $list = $result->getArray(); // select 结果集
    // 遍历结果集
    foreach ($list as $row)
    {
        var_dump($row);
    }
}
```

##### chunkByOffset

利用`limit`进行查询驱动分块，效率与一般分页查询没区别，相对`chunkById`兼容更多的场景，如果追求性能还是推荐`chunkById`。

| 参数 | 类型 | 说明                                                        |
| ------ | ------ |-----------------------------------------------------------|
| limit  | int    | 每次查询的块大小                                                  |

> 对于`ORM`下使用，由于不是一次性载入全部数据，预加载功能对于每个块都是重复加载的，建议根据实际情况决定是否实现一个缓存查询来替代预加载。

```php
// 按 10 条每块遍历全部符合条件的行。

foreach (TestModel::query()->chunkByOffset(10) as $result)
{
    $list = $result->getArray(); // select 结果集
    // 遍历结果集
    foreach ($list as $row)
    {
        var_dump($row);
    }
}
```

##### 还有一个更简单的用法

```php
// chunk by id
foreach (TestModel::query()->chunkById(10, 'id')->each() as $row)
{
    var_dump($row);
}
// chunk by offset
foreach (TestModel::query()->chunkByOffset(10)->each() as $row)
{
    var_dump($row);
}
```

#### 游标查询

游标查询能对于查询大结果集时能有效节约应用内存消耗，对于数据库的消耗与`select`无差别。。
> 由于不是一次性载入全部数据，与预加载功能不兼容，不推荐对于游标查询进行任何的模型关联操作。

```php
foreach (TestModel::query()->cursor() as $data)
{
    var_dump($data); // 输出单个模型
}
```

### 聚合函数

```php
TestModel::count();
TestModel::sum('id');
```

### 批量更新

```php
// update tb_test set a = 'abc' where id > 5
TestModel::updateBatch([
        'a'	=> 'abc',
    ], [
        'id' => ['>', 5]
    ]);
```

### 批量删除

```php
// delete tb_test where a = 'abc' and id > 5
TestModel::deleteBatch([
    'a' => ['=', 'abc'],
    'id' => ['>', 5]
]);
```

### 查询器

```php
$testModel = TestModel::query()->where()->join()->select()->get();
// $testModel 依然是 TestModel 类型
```

### 对象转数组

**将当前对象作为数组返回：**

属性的值，如果是对象，那依然会保持原样。只保证第一层是数组。

```php
$testModel = TestModel::find(1, 'abc');
$array = $testModel->toArray();
```

**将当前模型转为数组：**

包括属性的值也会被转为数组

```php
$testModel = TestModel::find(1, 'abc');
$array = $testModel->convertToArray(); // 过滤注解定义的隐藏属性
$array = $testModel->convertToArray(false); // 不过滤
```

### 转换模型数组为模型

```php
$list = TestModel::select();
$arrayList = TestModel::convertListToArray($list); // 过滤注解定义的隐藏属性
$arrayList = TestModel::convertListToArray($list, false); // 不过滤
```

### 手动获取/设置模型序列化字段

默认情况下，根据 `@Column` 注解定义字段，`@Serializable`、`@Serializables` 干预序列化（toArray、json_encode）后的字段。

现在你也可以手动干预了，示例如下：

```php
$member->__getSerializedFields(); // 获取，默认为 null 则使用默认规则

$member->__setSerializedFields(['username', 'password']); // 手动干预，序列化后只有username、password字段
$member->__setSerializedFields(null); // 设为默认
```

### Fork 模型

Fork 模型特性，支持在运行阶段创建一个新的模型类，这个类从原模型继承。

并且支持指定新模型类使用的：数据库名、数据表名、连接池名。

方法定义：

```php
/**
 * Fork 模型.
 *
 * @return class-string<static>
 */
public static function fork(?string $tableName = null, ?string $poolName = null)
```

例子：

```php
$newClassName = TestModel::fork(); // 不修改任何参数，返回新的类名（这个用法没有实际意义）

$newClassName = TestModel::fork('tb_test2'); // 指定表名
$newClassName = TestModel::fork('db2.tb_test2'); // 指定数据库名和表名

$newClassName = TestModel::fork(null, 'pool2'); // 指定连接池名

$newClassName = TestModel::fork('tb_test2', 'pool2'); // 同时指定
```
