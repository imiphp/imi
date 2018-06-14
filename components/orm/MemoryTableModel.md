## 介绍

基于 Swoole Table 跨进程共享内存表的模型。通过注解定义，框架底层自动创建SwooleTable，直接使用模型操作，方便快捷！

## 模型定义

喜闻乐见的对命名空间、类名无要求，只要按照规定写注解即可！

具体定义看下面代码：

```php
namespace Test;

use Imi\Model\MemoryTableModel;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\MemoryTable;

/**
 * @MemoryTable(name="test")
 */
class MTest extends MemoryTableModel
{
	/**
	 * @Column(name="str",type="string",length=128)
	 * @var string
	 */
	protected $str;

	/**
	 * @Column(name="int",type="int")
	 * @var int
	 */
	protected $int;

	/**
	 * @Column(name="float",type="float")
	 * @var float
	 */
	protected $float;

	/**
	 * Get the value of str
	 *
	 * @return  string
	 */ 
	public function getStr()
	{
		return $this->str;
	}

	/**
	 * Set the value of str
	 *
	 * @param  string  $str
	 *
	 * @return  self
	 */ 
	public function setStr(string $str)
	{
		$this->str = $str;

		return $this;
	}

	/**
	 * Get the value of int
	 *
	 * @return  int
	 */ 
	public function getInt()
	{
		return $this->int;
	}

	/**
	 * Set the value of int
	 *
	 * @param  int  $int
	 *
	 * @return  self
	 */ 
	public function setInt(int $int)
	{
		$this->int = $int;

		return $this;
	}

	/**
	 * Get the value of float
	 *
	 * @return  float
	 */ 
	public function getFloat()
	{
		return $this->float;
	}

	/**
	 * Set the value of float
	 *
	 * @param  float  $float
	 *
	 * @return  self
	 */ 
	public function setFloat(float $float)
	{
		$this->float = $float;

		return $this;
	}
}
```

需要使用注解将表、字段属性全部标注。并且写上`get`和`set`方法，很快我们将推出一个生成器，无需手动定义。

`@MemoryTable(name="test")` 是指定SwooleTable的名称
`@Column(name="str",type="string",length=128)`中的`name`代表字段名，`type`支持`string/int/float`，`string`类型必须设置`length`

## 模型操作

### 查找一条记录

```php
$key = 'abc';
$model = MTest::find($key);
```

### 查询多条记录

```php
$list = MTest::select();
// $list 为 MTest[] 类型
```

### 保存记录

```php
$model = MTest::newInstance();
$model->setKey('abc');
$model->setStr('aaa');
$model->setInt(123);
$model->setFloat(4.56);
$model->save();
```

### 删除记录

```php
$model = MTest::find('abc');
$model->delete();
```

### 批量删除

```php
// 两种方式
MTest::deleteBatch('k1', 'k2');
MTest::deleteBatch(['k1', 'k2']);
```

### 统计数量

```php
MTest::count();
```

### 获取键

```php
$model = MTest::find('abc');
$model->getKey();
```

### 设置键

```php
$model = MTest::find('abc');
$model->setKey('def');
```