# 多对多关联

比如我们要关联用户和角色之间的关系，就要用到多对多。多对多需要一张中间表来做关联。

具体示例代码可以看imi-demo项目，下面仅为简单展示。

## 定义

多对多关联会用到的注解：

`@ManyToMany`、`@JoinFromMiddle`、`@JoinToMiddle`、`@AutoSelect`、`@AutoInsert`、`@AutoUpdate`、`@AutoSave`、`@AutoDelete`

如 imi-demo 中代码所示，类定义了一个`$userRole`属性和`$role`属性。

`$userRole`属性定义的是中间模型，指定中间模型为`UserRole`，右侧表模型`Role`，右侧表模型数据到时会赋值到`$role`属性中。

`User`模型使用id与中间模型的`user_id`关联，中间模型使用`role_id`与右侧模型`id`关联。

允许自动查询、插入、更新、保存、删除时，自动处理关联模型数据。

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
	 * @ManyToMany(model="Role", middle="UserRole", rightMany="role")
	 * @JoinToMiddle(field="id", middleField="user_id")
	 * @JoinFromMiddle(middleField="role_id", field="id")
	 * 
	 * @AutoInsert
	 * @AutoUpdate
	 * @AutoSave
	 * @AutoDelete
	 *
	 * @var \Imi\Util\ArrayList
	 */
	protected $userRole;
	
	/**
	 * Get the value of userRole
	 *
	 * @return  \Imi\Util\ArrayList
	 */ 
	public function getUserRole()
	{
		return $this->userRole;
	}

	/**
	 * Set the value of userRole
	 *
	 * @param  \Imi\Util\ArrayList  $userRole
	 *
	 * @return  self
	 */ 
	public function setUserRole($userRole)
	{
		$this->userRole = $userRole;

		return $this;
	}

	/**
	 * 
	 *
	 * @var \Imi\Util\ArrayList
	 */
	protected $role;

	/**
	 * Get the value of role
	 *
	 * @return  \ImiDemo\HttpDemo\MainServer\Model\ArrayList[]
	 */ 
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * Set the value of role
	 *
	 * @param  \ImiDemo\HttpDemo\MainServer\Model\ArrayList[]  $role
	 *
	 * @return  self
	 */ 
	public function setRole($role)
	{
		$this->role = $role;

		return $this;
	}

	// 其它这边省略……
}
```

## 查询

### find

```php
$model = UserWithFriend::find(1);
// 可以取到关联关系及右侧模型数据
var_dump($model->userRole);
var_dump($model->role);
```

### select

```php
$list = UserWithFriend::select();
foreach($list as $item)
{
	// 可以取到关联关系及右侧模型数据
	var_dump($item->userRole);
	var_dump($item->role);
}
```

## 插入

```php
$user = UserWithFriend::newInstance();
$user->username = Random::letterAndNumber(6, 16);
$user->ex->intro = '这个人很懒，什么也没留下';
// 插入关联关系
$user->userRole->append(
	UserRole::newInstance(['role_id'=>1]), 
	UserRole::newInstance(['role_id'=>2])
);
$result = $user->insert();
```

## 更新

```php
$user = UserWithFriend::find(1);
$user->ex->intro = '这个人很懒，什么也没留下-' . date('Y-m-d H:i:s');
$user->userRole->clear();
// 更新关联关系
$user->userRole->append(
	UserRole::newInstance(['role_id'=>998])
);
$result = $user->update();
```

## 保存

和insert、update同理，就不作演示了。

## 删除

```php
$user = UserWithFriend::find(1);
// 删除数据和关联关系
$result = $user->delete();
if($result->isSuccess())
{
	echo 'success';
}
```