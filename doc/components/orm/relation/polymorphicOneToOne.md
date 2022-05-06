# 多态一对一关联

[toc]

比如我们有一个用户表，一个团队表，他们和头像表相关联。

```php
mysql> desc tb_user;
+----------+------------------+------+-----+---------+----------------+
| Field    | Type             | Null | Key | Default | Extra          |
+----------+------------------+------+-----+---------+----------------+
| id       | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| username | varchar(32)      | NO   |     | NULL    |                |
| age      | tinyint(3)       | NO   |     | NULL    |                |
+----------+------------------+------+-----+---------+----------------+

mysql> desc tb_team;
+-------+------------------+------+-----+---------+----------------+
| Field | Type             | Null | Key | Default | Extra          |
+-------+------------------+------+-----+---------+----------------+
| id    | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| name  | varchar(32)      | NO   |     | NULL    |                |
+-------+------------------+------+-----+---------+----------------+

mysql> desc tb_avatar;
+-------------+------------------+------+-----+---------+----------------+
| Field       | Type             | Null | Key | Default | Extra          |
+-------------+------------------+------+-----+---------+----------------+
| id          | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| type        | tinyint(4)       | NO   | MUL | NULL    |                |
| relation_id | int(10) unsigned | NO   |     | NULL    |                |
| url         | varchar(255)     | NO   |     | NULL    |                |
+-------------+------------------+------+-----+---------+----------------+
```

## 定义

多态一对一关联会用到的注解：

`@PolymorphicOneToOne`、`@PolymorphicToOne`、`@JoinFrom`、`@JoinTo`、`@AutoSelect`、`@AutoInsert`、`@AutoUpdate`、`@AutoSave`、`@AutoDelete`

如 imi-demo 中代码所示，定义了一个`$avatar`属性，这个属性关联`Avatar`模型。

`User`中`id`与`Avatar`中`relation_id`关联，并且`type`为`1`的才关联。

### User 模型

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
 * @property \ImiDemo\HttpDemo\MainServer\Model\Avatar $avatar
 */
class User extends Model
{
    /**
     * 头像
     * 
     * @PolymorphicOneToOne(model=ImiDemo\HttpDemo\MainServer\Model\Avatar::class, type="type", typeValue=1)
     * @JoinTo("relation_id")
     * @AutoSave
     * @AutoDelete
     *
     * @var \ImiDemo\HttpDemo\MainServer\Model\Avatar
     */
    protected $avatar;

    /**
     * Get 头像
     *
     * @return  \ImiDemo\HttpDemo\MainServer\Model\Avatar
     */ 
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set 头像
     *
     * @param  \ImiDemo\HttpDemo\MainServer\Model\Avatar  $avatar  头像
     *
     * @return  self
     */ 
    public function setAvatar(\ImiDemo\HttpDemo\MainServer\Model\Avatar $avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

}
```

`@PolymorphicOneToOne`注解中，`type`代表在`Avatar`模型中的字段名，`typeValue`代表匹配的值。

### Avatar 模型

```php
/**
 * Avatar
 * @Entity
 * @Table(name="tb_avatar", id={"id"})
 * @property int $id
 * @property int $type
 * @property int $relationId
 * @property string $url
 */
class Avatar extends Model
{
    /**
     * id
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
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
     * type
     * @Column(name="type", type="tinyint", length=4, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int
     */
    protected $type;

    /**
     * 获取 type
     *
     * @return int
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * 赋值 type
     * @param int $type type
     * @return static
     */ 
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * relation_id
     * @Column(name="relation_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int
     */
    protected $relationId;

    /**
     * 获取 relationId
     *
     * @return int
     */ 
    public function getRelationId()
    {
        return $this->relationId;
    }

    /**
     * 赋值 relationId
     * @param int $relationId relation_id
     * @return static
     */ 
    public function setRelationId($relationId)
    {
        $this->relationId = $relationId;
        return $this;
    }

    /**
     * url
     * @Column(name="url", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $url;

    /**
     * 获取 url
     *
     * @return string
     */ 
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 赋值 url
     * @param string $url url
     * @return static
     */ 
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * 对应用户
     * 
     * @PolymorphicToOne(model=ImiDemo\HttpDemo\MainServer\Model\User::class, modelField="id", type="type", typeValue=1, field="relation_id")
     * @AutoSelect(false)
     *
     * @var \ImiDemo\HttpDemo\MainServer\Model\User
     */
    protected $user;

    /**
     * Get 对应用户
     *
     * @return  \ImiDemo\HttpDemo\MainServer\Model\User
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set 对应用户
     *
     * @param  \ImiDemo\HttpDemo\MainServer\Model\User  $user  对应用户
     *
     * @return  self
     */ 
    public function setUser(\ImiDemo\HttpDemo\MainServer\Model\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * 对应用户
     * 
     * @PolymorphicToOne(model=ImiDemo\HttpDemo\MainServer\Model\Team::class, modelField="id", type="type", typeValue=2, field="relation_id")
     * @AutoSelect(false)
     *
     * @var \ImiDemo\HttpDemo\MainServer\Model\Team
     */
    protected $team;

    /**
     * Get 对应用户
     *
     * @return  \ImiDemo\HttpDemo\MainServer\Model\Team
     */ 
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set 对应用户
     *
     * @param  \ImiDemo\HttpDemo\MainServer\Model\Team  $team  对应用户
     *
     * @return  self
     */ 
    public function setTeam(\ImiDemo\HttpDemo\MainServer\Model\Team $team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * 对应用户
     * 
     * @PolymorphicToOne(model=ImiDemo\HttpDemo\MainServer\Model\User::class, modelField="id", type="type", typeValue=1, field="relation_id")
     * @PolymorphicToOne(model=ImiDemo\HttpDemo\MainServer\Model\Team::class, modelField="id", type="type", typeValue=2, field="relation_id")
     * @AutoSelect(false)
     *
     * @var \ImiDemo\HttpDemo\MainServer\Model\User|\ImiDemo\HttpDemo\MainServer\Model\Team
     */
    protected $relationModel;

    /**
     * Get 对应用户
     *
     * @return  \ImiDemo\HttpDemo\MainServer\Model\User|\ImiDemo\HttpDemo\MainServer\Model\Team
     */ 
    public function getRelationModel()
    {
        return $this->relationModel;
    }

    /**
     * Set 对应用户
     *
     * @param  \ImiDemo\HttpDemo\MainServer\Model\User|\ImiDemo\HttpDemo\MainServer\Model\Team  $relationModel  对应用户
     *
     * @return  self
     */ 
    public function setRelationModel($relationModel)
    {
        $this->relationModel = $relationModel;

        return $this;
    }
}
```

`Avatar` 模型中，`$user`和`$team`是指定类型的关联，只能取到对应模型的数据。

`$relationModel`会根据当前模型的`type`值，查询出对应模型的数据，类型不固定。

## 查询

> 常见的增删改查就不写了，和一对一关联一样用法

### 头像模型反查用户模型

#### 智能类型查询

```php
$avatar = Avatar::find(1);
$avatar->queryRelations('relationModel');
$user = $avatar->relationModel;
```

#### 指定类型查询

```php
$avatar = Avatar::find(1);
$avatar->queryRelations('user');
$user = $avatar->user;
```

#### 同时查询多个

```php
$avatar = Avatar::find(1);
$avatar->queryRelations('user', 'relationModel');
$user1 = $avatar->user;
$user2 = $avatar->relationModel;
```
