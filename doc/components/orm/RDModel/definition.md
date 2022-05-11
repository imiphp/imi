# 数据库模型

[toc]

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

> 建议使用模型生成工具：<https://doc.imiphp.com/v2.1/dev/generate/model.html>

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

将模型设为非 bean 类：

`@Entity(camel=true, bean=false)`

> 非 bean 类性能更好，但无法用 AOP 切入类，事件也不生效，一般模型建议使用非 bean 类模式。

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

设定 JSON 序列化时的配置

写在类上可以让模型类中所有 Json 字段生效。

写在属性上，可以覆盖写在类上的注解。

不使用 Unicode 编码转换中文：`@JsonEncode(JSON_UNESCAPED_UNICODE)`

完整参数：`@JsonEncode(flags=4194624, depth=512)`

> `4194624 === \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE`

> 参数含义同 `json_encode()`

### @JsonDecode

设定 JSON 反序列化时的配置

写在类上可以让模型类中所有 Json 字段生效。

写在属性上，可以覆盖写在类上的注解。

完整参数：`@JsonDecode(associative=true, depth=512, flags=0, wrap=\Imi\Util\LazyArrayObject::class)`

> 除 `$wrap` 外其它参数含义同 `json_decode()`

**$wrap 参数说明：**

`$wrap` 反序列化数据的包装，如果是对象或者数组时有效。支持类名、函数名。

类名：

```php
// 将此类的对象作为属性值
class WrapClass
{
    /**
     * @param mixed $data json_decode() 结果
     */
    public function __construct($data)
    {
    }
}
```

函数名：

```php
/**
 * @param mixed $data json_decode() 结果
 * @return mixed
 */
function demoWrap($data)
{
    return $data; // 返回值作为属性值
}
```

### @Column

写在属性上，定义字段列

`@Column(name="字段名", type="字段类型", length="长度", nullable="是否允许为空true/false", accuracy="精度，小数位后几位", default="默认值", isPrimaryKey="是否为主键true/false", primaryKeyIndex="联合主键中的第几个，从0开始", isAutoIncrement="是否为自增字段true/false", virtual="虚拟字段，不参与数据库操作true/false", updateTime=true, createTime=true, reference="引用字段名，作为引用字段的别名使用，拥有同等的读写能力，需要将virtual设为true", unsigned=false)`

> 当你指定`type=json`时，写入数据库时自动`json_encode`，从数据实例化到对象时自动`json_decode`

> 当你指定`type=list`并且设置了`listSeparator`分割符时，写入数据库时自动`implode`，从数据实例化到对象时自动`explode`

`updateTime`：save/update 模型时是否将当前时间写入该字段。支持 date/time/datetime/timestamp/year/int/bigint。当字段为 int 类型，写入秒级时间戳。当字段为 bigint 类型，写入毫秒级时间戳。

`createTime`：save/insert 模型时是否将当前时间写入该字段，**save时表有自增ID主键才支持**；支持 date/time/datetime/timestamp/year/int/bigint；当字段为 int 类型，写入秒级时间戳；当字段为 bigint 类型，写入毫秒级时间戳。

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
    'ex'    =>	[
        'userId'	=>	123,
    ],
];
```
