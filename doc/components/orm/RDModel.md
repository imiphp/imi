# 数据库表模型

## 介绍

传统关系型数据库（MySQL）的模型，日常增删改查完全够用，支持复合主键、联合主键。

需要注意的是，imi 的模型与传统 TP 等框架中的模型概念有些差别。

imi 的模型类里一般不写逻辑代码，模型类的一个对象就代表一条记录，并且所有字段都需要有值（除非你不定义指定的字段）。

## 模型定义

喜闻乐见的对命名空间、类名无要求，只要按照规定写注解即可！

`@Entity` 注解为定义实体类

`@Table` 注解为定义数据表

`@Column` 注解为定义字段

`@DDL` 定义表结构的 SQL 语句

> 建议使用模型生成工具：<https://doc.imiphp.com/v2.0/dev/generate/model.html>

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

### @DDL

写在类上，定义表结构

**用法：**

```php
/**
 * ArticleBase
 * @Entity
 * @Table(name="tb_article", id={"id"})
 * @DDL("CREATE TABLE `tb_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT")
 * @property int $id 
 * @property string $title 
 * @property string $content 
 * @property string $time 
 */
abstract class ArticleBase extends Model
{
}
```

### @JsonEncode

写在类上，设定 JSON 序列化时的配置

不使用 Unicode 编码转换中文：`@JsonEncode(JSON_UNESCAPED_UNICODE)`

完整参数：`@JsonEncode(flags=0, depth=512)`

### @Column

写在属性上，定义字段列

`@Column(name="字段名", type="字段类型", length="长度", nullable="是否允许为空true/false", accuracy="精度，小数位后几位", default="默认值", isPrimaryKey="是否为主键true/false", primaryKeyIndex="联合主键中的第几个，从0开始", isAutoIncrement="是否为自增字段true/false", virtual="虚拟字段，不参与数据库操作true/false", updateTime=true)`

> 当你指定`type=json`时，写入数据库时自动`json_encode`，从数据实例化到对象时自动`json_decode`

> 当你指定`type=list`并且设置了`listSeparator`分割符时，写入数据库时自动`implode`，从数据实例化到对象时自动`explode`

`updateTime`：save/update 模型时是否将当前时间写入该字段。支持 date/time/datetime/timestamp/year/int/bigint。当字段为 int 类型，写入秒级时间戳。当字段为 bigint 类型，写入毫秒级时间戳。

### @Sql

为虚拟字段定义 SQL 语句，模型查询时自动带上改字段。

`@Sql("SQL 语句")`

如果 `@Column` 注解定义了 `name` 属性，则将 `name` 作为字段别名；如果未定义，则使用属性名称作为别名。

示例：

```php
<?php

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Serializables;
use Imi\Model\Annotation\Sql;
use Imi\Test\Component\Model\Base\MemberBase;

/**
 * Member.
 *
 * @Inherit
 * @Serializables(mode="deny", fields={"password"})
 */
class MemberWithSqlField extends MemberBase
{
    /**
     * @Column(name="a", virtual=true)
     * @Sql("1+1")
     *
     * @var int
     */
    public $test1;

    /**
     * @Column(virtual=true)
     * @Sql("2+2")
     *
     * @var int
     */
    public $test2;

    /**
     * Set the value of test1.
     *
     * @param int $test1
     *
     * @return self
     */
    public function setTest1(int $test1)
    {
        $this->test1 = $test1;

        return $this;
    }

    /**
     * Get the value of test1.
     *
     * @return int
     */
    public function getTest1()
    {
        return $this->test1;
    }

    /**
     * Set the value of test2.
     *
     * @param int $test2
     *
     * @return self
     */
    public function setTest2(int $test2)
    {
        $this->test2 = $test2;

        return $this;
    }

    /**
     * Get the value of test2.
     *
     * @return int
     */
    public function getTest2()
    {
        return $this->test2;
    }
}
```

查询时的 SQL 语句：

```sql
select `tb_member`.*,(1+1) as `a`,(2+2) as `test2` from `tb_member` where `id`=:id limit 1
```

### @JsonNotNull

写在属性上，无参数。

当字段值不为 null 时才序列化到 json

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

查询结果返回模型对象：

```php
$query = TestModel::query();
```

查询结果返回数组：

```php
$query = TestModel::dbQuery();
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

### 关联模型预加载

```php
$list = TestModel::query()
                ->with('关联字段名') // 单个
                ->with(['字段名1', '字段名2']) // 多个
                ->where('id', '=', 1)->select()->getArray();
```
