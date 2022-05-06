# Redis 模型

[toc]

## 介绍

Redis 模型适合固定结构的数据结构，可以跟关系型数据库的模型一样，无需关心直接的操作，增删改查一把梭。

## 模型定义

喜闻乐见的对命名空间、类名无要求，只要按照规定写注解即可！

具体定义看下面代码：

```php
<?php
namespace ImiDemo\HttpDemo\MainServer\Model;

use Imi\Model\RedisModel;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\RedisEntity;

/**
 * Test
 * @Entity
 * @RedisEntity(poolName="redis", key="{id}-{name}")
 */
class TestRedisModel extends RedisModel
{
	/**
	 * id
	 * @Column(name="id")
	 * @var int
	 */
    protected $id;

    /**
     * 获取 id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 赋值 id
     * @param int $id id
     * @return static
     */
    public function setId($id)
    {
    	$this->id = $id;
		return $this;
	}

	/**
	 * name
	 * @Column(name="name")
	 * @var string
	 */
	protected $name;

	/**
	 * 获取 name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * 赋值 name
	 * @param string $name name
	 * @return static
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * age
	 * @Column(name="age")
	 * @var string
	 */
	protected $age;

	/**
	 * 获取 age
	 *
	 * @return string
	 */
	public function getAge()
	{
		return $this->age;
	}

	/**
	 * 赋值 age
	 * @param string $age age
	 * @return static
	 */
	public function setAge($age)
	{
		$this->age = $age;
		return $this;
	}
}
```

需要使用注解将表、字段属性全部标注。并且写上`get`和`set`方法。

#### @RedisEntity

指定当前类为Redis实体类

**可选属性：**

`poolName`redis连接池名称

`db`第几个库，不传为null时使用连接池默认配置

`key`键，支持定义多个参数，默认为`{key}`，在`RedisModel`中已经预先定义了`setKey()`和`getKey()`方法

`member`，规则同`key`，仅在`storage`为`hash`时有效

`ttl`数据默认的过期时间，null为永不过期，hash 存储模式不支持过期

`storage`Redis 实体类存储模式，支持：string、hash、hash_object

`formatter` 格式；可选：`Imi\Util\Format\Json`、`Imi\Util\Format\PhpSerialize`，或者可以自行实现`Imi\Util\Format\IFormat`接口

**storage 属性说明：**

- string

字符串模式，使用 set/get 存序列化后的对象

- hash

hash 模式，使用 hset/hget 存序列化后的对象

- hash_object

hash 对象模式，使用 hset/hget，将对象存到一个 key 中，member 为字段名

#### @Column

Redis模型中只有name生效

## 模型操作

所有操作都是依据上面定义的`TestRedisModel`

### 查找一条记录

```php
// 读取-直接传key
$model2 = TestRedisModel::find('123-imi');

// 读取-传参数组成
$model3 = TestRedisModel::find([
	'id'	=>	'123',
	'name'	=>	'imi'
]);
```

### 查询多条记录

```php
$list = TestRedisModel::select('kkk', '123-imi', [
	'id'	=>	'123',
	'name'	=>	'imi'
]);
foreach($list as $item)
{
	var_dump($item->toArray());
}
```

### 保存记录

```php
$model = TestRedisModel::newInstance();
$model->setId(123);
$model->setName('imi');
$model->setAge(100);
$model->save();

```

### 删除记录

```php
$model2 = TestRedisModel::find('123-imi');
$model2->delete();
```

### 批量删除

```php
// 删除键为key和123-imi的数据
// 123-imi是以RedisEntity中定义为准
TestRedisModel::deleteBatch('kkk', [
	'id'	=>	'123',
	'name'	=>	'imi'
]);
```
