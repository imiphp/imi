# 一对多关联

[toc]

比如我们有一个用户表，每个用户都可以发布文章，用户和文章就是一对多的关系。

当然imi-demo里一对多的例子选的不好，不过不重要，理解意思就行。

## 定义

一对多关联会用到的注解：

`@OneToMany`、`@JoinFrom`、`@JoinTo`、`@AutoSelect`、`@AutoInsert`、`@AutoUpdate`、`@AutoSave`、`@AutoDelete`

如 imi-demo 中代码所示，`UserWithFriend`继承`User`。类定义了一个`$friends`属性，这个属性关联`UserFriend`模型。

`User`中`id`与`UserFriend`中`user_id`关联，你可能会发现没有`@JoinFrom`注解，没有的话默认取左侧模型的主键。

允许自动查询、插入、更新、删除时，自动处理`UserFriend`模型数据，当更新时，会删除不存在的数据。

```php
/**
 * User
 * @Entity
 * @Table(name="tb_user", id={"id"})
 * @property int $id
 * @property string $username
 * @property \ImiDemo\HttpDemo\MainServer\Model\UserEx $ex
 * @property \Imi\Util\ArrayList $friends
 */
class UserWithFriend extends User
{
	/**
	 * @OneToMany("UserFriend")
	 * @JoinTo("user_id")
	 * @AutoInsert(true)
	 * @AutoUpdate(orphanRemoval=true)
	 * @AutoDelete(true)
	 *
	 * @var \Imi\Util\ArrayList
	 */
	protected $friends;

	/**
	 * Get the value of friends
	 *
	 * @return  \ImiDemo\HttpDemo\MainServer\Model\UserFriend[]
	 */ 
	public function getFriends()
	{
		return $this->friends;
	}

	/**
	 * Set the value of friends
	 *
	 * @param  \ImiDemo\HttpDemo\MainServer\Model\UserFriend[]  $friends
	 *
	 * @return  self
	 */ 
	public function setFriends($friends)
	{
		$this->friends = $friends;

		return $this;
	}

}
```

## 查询

### find

```php
$model = UserWithFriend::find(1);
var_dump($model->friends); // friends数据可以直接取到，是UserFriend实例
```

### select

```php
$list = UserWithFriend::select();
foreach($list as $item)
{
	var_dump($item->friends); // friends数据可以直接取到，是UserFriend实例
}
```

## 插入

```php
$user = UserWithFriend::newInstance();
$user->username = 'Yurun';
// 由于继承了User类，所以一对一关系还是存在，依旧可以使用
$user->ex->intro = '这个人很懒，什么也没留下';
// 在朋友关系列表中增加2项
$user->friends->append(
	UserFriend::newInstance(['friend_user_id'=>1]), 
	UserFriend::newInstance(['friend_user_id'=>2])
);
$result = $user->insert();
```

## 更新

```php
$user = UserWithFriend::find(1);
$user->ex->intro = '这个人很懒，什么也没留下-' . date('Y-m-d H:i:s');

// 下面注释的代码，是把多余的记录删除，只保留id为4这个
// $friendIds = [4];
// $user->friends->clear();
// $updateItems = UserFriend::query()->where('user_id', '=', $user->id)->whereIn('friend_user_id', $friendIds)->select()->getArray();
// $user->friends->append(...$updateItems);
// $ids = ObjectArrayHelper::column($updateItems, 'friend_user_id');
// foreach(array_diff($friendIds, $ids) as $id)
// {
// 	$user->friends->append(UserFriend::newInstance([
// 		'friend_user_id'	=>	$id,
// 	]));
// }

// 下面的是简单的增加两项关联
$user->friends->append(
	UserFriend::newInstance(['friend_user_id'=>1]), 
	UserFriend::newInstance(['friend_user_id'=>2])
);

$result = $user->update();
```

## 保存

和insert、update同理，就不作演示了。

## 删除

```php
$user = UserWithFriend::find(1);
// 删除ID为1的记录，UserEx对应表也会删除这条关联记录
$result = $user->delete();
if($result->isSuccess())
{
	echo 'success';
}
```
