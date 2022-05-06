# 一对一关联

[toc]

比如我们有一个用户表，另外有一个个人资料表，他们之间的关联就是一对一的关系。

## 定义

一对一关联会用到的注解：

`@OneToOne`、`@JoinFrom`、`@JoinTo`、`@AutoSelect`、`@AutoInsert`、`@AutoUpdate`、`@AutoSave`、`@AutoDelete`

如 imi-demo 中代码所示，定义了一个`$ex`属性，这个属性关联`UserEx`模型。

`User`中`id`与`UserEx`中`user_id`关联。

允许自动查询、保存、删除时，自动处理`UserEx`模型数据。

```php
/**
 * User
 * @Entity
 * @Table(name="tb_user", id={"id"})
 * @property int $id
 * @property string $username
 * @property \ImiDemo\HttpDemo\MainServer\Model\UserEx $ex
 * @property \Imi\Util\ArrayList $userRole
 * @property \Imi\Util\ArrayList $role
 */
class User extends Model
{
	/**
	 * @OneToOne("UserEx")
	 * @JoinFrom("id")
	 * @JoinTo("user_id")
	 * @AutoSave(true)
	 * @AutoDelete
	 *
	 * @var \ImiDemo\HttpDemo\MainServer\Model\UserEx
	 */
	protected $ex;

	/**
	 * Get the value of ex
	 *
	 * @return  \ImiDemo\HttpDemo\MainServer\Model\UserEx
	 */ 
	public function getEx()
	{
		return $this->ex;
	}

	/**
	 * Set the value of ex
	 *
	 * @param  \ImiDemo\HttpDemo\MainServer\Model\UserEx  $ex
	 *
	 * @return  self
	 */ 
	public function setEx(\ImiDemo\HttpDemo\MainServer\Model\UserEx $ex)
	{
		$this->ex = $ex;

		return $this;
	}

	// 其它这边省略……
```

## 查询

### find

```php
$model = User::find(1);
var_dump($model->ex); // ex数据可以直接取到，是UserEx实例
```

### select

```php
$list = User::select();
foreach($list as $item)
{
	var_dump($item->ex); // ex数据可以直接取到，是UserEx实例
}
```

## 插入

```php
$user = User::newInstance();
$user->username = 'User123';

// 对UserEx实例赋值
$user->ex->intro = '这个人很懒，什么也没留下';

// 一句话，数据插入两张表
$result = $user->insert();

var_dump($result->getLastInsertId());
```

## 更新

```php
$user = User::find(1);
$user->ex->intro = '这个人很懒，什么也没留下-' . date('Y-m-d H:i:s');

// 更新ID为1的记录
// 对User对象执行update，同时也会让UserEx做update
$result = $user->update();
```

## 保存

和insert、update同理，就不作演示了。

## 删除

```php
$user = User::find(1);
// 删除ID为1的记录，UserEx对应表也会删除这条关联记录
$result = $user->delete();
if($result->isSuccess())
{
	echo 'success';
}
```