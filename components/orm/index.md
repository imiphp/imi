## 介绍

目前实现了最基本最简单的模型 ORM 操作，日常增删改查完全够用，支持复合主键、联合主键。

## 模型定义

喜闻乐见的对命名空间、类名无要求，只要按照规定写注解即可！

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

需要使用注解将表、字段属性全部标注。并且写上`get`和`set`方法，很快我们将推出一个生成器，无需手动定义。

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