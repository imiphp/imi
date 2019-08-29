# 多态一对多关联

比如我们有一张文章表，一张书籍表，一张评论表。文章和书籍的评论记录都在同一张评论表中。

```php
mysql> desc tb_article;
+---------+------------------+------+-----+---------+----------------+
| Field   | Type             | Null | Key | Default | Extra          |
+---------+------------------+------+-----+---------+----------------+
| id      | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| title   | varchar(32)      | NO   |     | NULL    |                |
| content | text             | NO   |     | NULL    |                |
+---------+------------------+------+-----+---------+----------------+

mysql> desc tb_book;
+-------+------------------+------+-----+---------+----------------+
| Field | Type             | Null | Key | Default | Extra          |
+-------+------------------+------+-----+---------+----------------+
| id    | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| title | varchar(32)      | NO   |     | NULL    |                |
+-------+------------------+------+-----+---------+----------------+

mysql> desc tb_comment;
+-------------+------------------+------+-----+---------+----------------+
| Field       | Type             | Null | Key | Default | Extra          |
+-------------+------------------+------+-----+---------+----------------+
| id          | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| content     | text             | NO   |     | NULL    |                |
| type        | tinyint(4)       | NO   |     | NULL    |                |
| relation_id | int(10) unsigned | NO   |     | NULL    |                |
+-------------+------------------+------+-----+---------+----------------+
```

## 定义

一对多关联会用到的注解：

`@PolymorphicOneToMany`、`@PolymorphicToOne`、`@JoinFrom`、`@JoinTo`、`@AutoSelect`、`@AutoInsert`、`@AutoUpdate`、`@AutoSave`、`@AutoDelete`

如 imi-demo 中代码所示，`Article`定义了一个`$comments`属性，这个属性关联`Comment`模型。

`Article`中`id`与`Comment`中`relation_id`关联，并且`type`为`1`的才关联。

### Article 模型

```php
/**
 * Article
 * @Entity
 * @Table(name="tb_article", id={"id"})
 * @property int $id
 * @property string $title
 * @property string $content
 * @property \Imi\Util\ArrayList $comments
 * @property \Imi\Util\ArrayList $taggables
 * @property \Imi\Util\ArrayList $tags
 */
class Article extends Model
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
	 * title
	 * @Column(name="title", type="varchar", length=32, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
	 * @var string
	 */
	protected $title;

	/**
	 * 获取 title
	 *
	 * @return string
	 */ 
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * 赋值 title
	 * @param string $title title
	 * @return static
	 */ 
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * content
	 * @Column(name="content", type="text", length=0, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
	 * @var string
	 */
	protected $content;

	/**
	 * 获取 content
	 *
	 * @return string
	 */ 
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * 赋值 content
	 * @param string $content content
	 * @return static
	 */ 
	public function setContent($content)
	{
		$this->content = $content;
		return $this;
	}

	/**
     * 评论
     * 
     * @PolymorphicOneToMany(model=ImiDemo\HttpDemo\MainServer\Model\Comment::class, type="type", typeValue=1)
     * @JoinTo("relation_id")
     * @AutoSave(orphanRemoval=true)
     * @AutoDelete
     *
     * @var \Imi\Util\ArrayList
     */
    protected $comments;

    /**
     * Get 评论
     *
     * @return  \ImiDemo\HttpDemo\MainServer\Model\Comment[]
     */ 
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set 评论
     *
     * @param  \ImiDemo\HttpDemo\MainServer\Model\Comment[]  $comments  评论
     *
     * @return  self
     */ 
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
	}
	
}
```

主要参考`$comments`上的注解，与多态一对多一样。

### Comment 模型

```php
/**
 * Comment
 * @Entity
 * @Table(name="tb_comment", id={"id"})
 * @property int $id
 * @property string $content
 * @property int $type
 * @property int $relationId
 */
class Comment extends Model
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
	 * content
	 * @Column(name="content", type="text", length=0, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
	 * @var string
	 */
	protected $content;

	/**
	 * 获取 content
	 *
	 * @return string
	 */ 
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * 赋值 content
	 * @param string $content content
	 * @return static
	 */ 
	public function setContent($content)
	{
		$this->content = $content;
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

}
```

## 查询

> 常见的增删改查就不写了，和一对多关联一样用法

### 评论模型反查文章模型

注解及用法与一对一相同
