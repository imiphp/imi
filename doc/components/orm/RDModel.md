# 数据库模型

## 介绍

传统关系型数据库的模型，日常增删改查完全够用，支持复合主键、联合主键。

## 模型定义

喜闻乐见的对命名空间、类名无要求，只要按照规定写注解即可！

`@Entity` 注解为定义实体类

`@Table` 注解为定义数据表

`@Column` 注解为定义字段

具体定义看下面代码：

```php
namespace Test;

use Imi\Model\Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;

/**
 * 定义为实体
 * @Entity
 * 指定实体为test，复合主键id和a
 * @Table(name="test", id={"id", "a"})
 */
class Test extends Model
{
    /**
     * ID
     * 字段id，类型int，长度10，是主键，第0个主键，是自增字段
     * @Column(name="id", type="int", length=10, isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
     * @var int
     */
    protected $id;

    /**
     * aaa
     * @Column(name="a", type="string", length=255, isPrimaryKey=true, primaryKeyIndex=1)
     * @var string
     */
    protected $a;

    /**
     * bbb
     * @Column(name="b", type="string", length=255)
     * @var string
     */
    protected $b;

    /**
     * ccc
     * @Column(name="c", type="string", length=255)
     * @var string
     */
    protected $c;

    /**
     * Get iD
     *
     * @return  int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set iD
     *
     * @param  int  $id  ID
     *
     * @return  self
     */ 
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get aaa
     *
     * @return  string
     */ 
    public function getA()
    {
        return $this->a;
    }

    /**
     * Set aaa
     *
     * @param  string  $a  aaa
     *
     * @return  self
     */ 
    public function setA(string $a)
    {
        $this->a = $a;

        return $this;
    }

    /**
     * Get bbb
     *
     * @return  string
     */ 
    public function getB()
    {
        return $this->b;
    }

    /**
     * Set bbb
     *
     * @param  string  $b  bbb
     *
     * @return  self
     */ 
    public function setB(string $b)
    {
        $this->b = $b;

        return $this;
    }

    /**
     * Get ccc
     *
     * @return  string
     */ 
    public function getC()
    {
        return $this->c;
    }

    /**
     * Set ccc
     *
     * @param  string  $c  ccc
     *
     * @return  self
     */ 
    public function setC(string $c)
    {
        $this->c = $c;

        return $this;
    }
}
```

需要使用注解将表、字段属性全部标注。并且写上`get`和`set`方法，可以使用模型生成工具生成。

模型中可以加入虚拟字段，通过注解`@Column(virtual=true)`，虚拟字段不参与数据库操作。

## 模型注解

### @Entity

写在类上，定义类为实体模型类

**用法：**

`@Entity`

序列化时不使用驼峰命名，使用原本的字段名：

`@Entity(false)`

### @Table

写在类上，定义数据表

**用法：**

`@Table('tb_user')`

指定数据库连接池：

`@Table(name='tb_user', dbPoolName='指定数据库连接池名')`

指定主键：

`@Table(name='tb_user', id='id')`

指定多个主键

`@Table(name='tb_user', id={'id1', 'id2'})`

### @Column

写在属性上，定义字段列

`@Column(name="字段名", type="字段类型", length="长度", nullable="是否允许为空true/false", accuracy="精度，小数位后几位", default="默认值", isPrimaryKey="是否为主键true/false", primaryKeyIndex="联合主键中的第几个，从0开始", isAutoIncrement="是否为自增字段true/false", virtual="虚拟字段，不参与数据库操作true/false", updateTime=true)`

> 当你指定`type=json`时，写入数据库时自动`json_encode`，从数据实例化到对象时自动`json_decode`

`updateTime`：save/update 模型时是否将当前时间写入该字段。支持 date/time/datetime/timestamp/year/int/bigint。当字段为 int 类型，写入秒级时间戳。当字段为 bigint 类型，写入毫秒级时间戳。

### @Serializable

写在属性上，序列化注解

**用法：**

禁止参与序列化（`toArray()`或`json_encode()`不包含该字段）：

`@Serializable(false)`

### @Serializables

写在类上，批量设置序列化注解，优先级低于针对属性单独设置的`@Serializable`注解

**用法：**

白名单（序列化后只显示id、name字段）：

`@Serializables(mode="allow", fields={"id", "name"})`

黑名单（序列化后，排除id、name字段）

`@Serializables(mode="deny", fields={"id", "name"})`

### @ExtractProperty

写在属性上，提取字段中的属性到当前模型

**用法：**

提取该属性中的`userId`值到当前模型中的`userId`：

`@ExtractProperty("userId")`

提取该属性中的`userId`值到当前模型中的`userId2`：

`@ExtractProperty(fieldName="userId", alias="userId2")`

支持多级提取到当前模型中的`userId2`：

```php
/**
 * @ExtractProperty(fieldName="ex.userId", alias="userId2")
 */
protected $xxx = [
    'ex'	=>	[
        'userId'	=>	123,
    ],
];
```

## 模型操作

### 查询记录

查询记录并返回一个模型实例。

```php
// where id = 1
$testModel = TestModel::find(1);
// 复合主键 where a = 1 and a = 'abc'
$testModel = TestModel::find(1, 'abc');
echo $testModel->getId();
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

查询总记录数、总页数：

```php
$page = 1;
$count = 10;
$data = TestModel::query()->paginate($page, $count);

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

不查询总记录数、总页数：

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

### 聚合函数

```php
TestModel::count();
TestModel::sum('id');
```

### 插入

```php
$testModel = TestModel::newInstance();
$testModel->setA('1');
$testModel->setB('1');
$testModel->setC('1');
$result = $testModel->insert();
// $result 用法同数据库中的 insert() 返回值用法
echo '插入的自增ID：', $testModel->getId();
```

### 更新

```php
$testModel = TestModel::find(1, 'abc');
$result = $testModel->update();
// $result 用法同数据库中的 update() 返回值用法
```

### 批量更新

```php
// update tb_test set a = 'abc' where id > 5
TestModel::updateBatch([
    'a'	=>	'abc',
], 'id > 5');
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

### 批量删除

```php
// delete tb_test set a = 'abc' where id > 5
TestModel::deleteBatch([
    'a'	=>	'abc',
], 'id > 5');
```

### 查询器

```php
$testModel = TestModel::query()->where()->join()->select()->get();
// $testModel 依然是 TestModel 类型
```

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

