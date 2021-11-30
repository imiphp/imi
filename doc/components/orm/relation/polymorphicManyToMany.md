# 多态多对多关联

比如我们有一张文章表，一张书籍表，一张标签表，一张标签关联表。文章和书籍共用标签库。

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

mysql> desc tb_tags;
+-------+------------------+------+-----+---------+----------------+
| Field | Type             | Null | Key | Default | Extra          |
+-------+------------------+------+-----+---------+----------------+
| id    | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| name  | varchar(32)      | NO   |     | NULL    |                |
+-------+------------------+------+-----+---------+----------------+

mysql> desc tb_taggables;
+---------------+------------------+------+-----+---------+-------+
| Field         | Type             | Null | Key | Default | Extra |
+---------------+------------------+------+-----+---------+-------+
| tag_id        | int(10) unsigned | NO   | PRI | NULL    |       |
| taggable_id   | int(10) unsigned | NO   | PRI | NULL    |       |
| taggable_type | tinyint(4)       | NO   | PRI | NULL    |       |
+---------------+------------------+------+-----+---------+-------+
```

## 定义

多对多关联会用到的注解：

`@PolymorphicManyToMany`、`@PolymorphicToMany`、`@JoinFromMiddle`、`@JoinToMiddle`、`@AutoSelect`、`@AutoInsert`、`@AutoUpdate`、`@AutoSave`、`@AutoDelete`

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
    // 省略其它……
    
    /**
     * 标签关联
     * 
     * @PolymorphicManyToMany(model="Tags", middle="Taggables", rightMany="tags", type="taggable_type", typeValue=1)
     * @JoinToMiddle(field="id", middleField="taggable_id")
     * @JoinFromMiddle(middleField="tag_id", field="id")
     * 
     * @AutoInsert
     * @AutoUpdate(orphanRemoval=true)
     * @AutoSave
     * @AutoDelete
     *
     * @var \Imi\Util\ArrayList
     */
    protected $taggables;

    /**
     * Get 标签关联
     *
     * @return  \Imi\Util\ArrayList
     */ 
    public function getTaggables()
    {
        return $this->taggables;
    }

    /**
     * Set 标签关联
     *
     * @param  \Imi\Util\ArrayList  $taggables  标签关联
     *
     * @return  self
     */ 
    public function setTaggables(\Imi\Util\ArrayList $taggables)
    {
    	$this->taggables = $taggables;

		return $this;
	}

	/**
	 * 关联标签
	 *
	 * @var \Imi\Util\ArrayList
	 */
	protected $tags;

	/**
	 * Get 关联标签
	 *
	 * @return  \Imi\Util\ArrayList
	 */ 
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * Set 关联标签
	 *
	 * @param  \Imi\Util\ArrayList  $tags  关联标签
	 *
	 * @return  self
	 */ 
	public function setTags($tags)
	{
		$this->tags = $tags;

		return $this;
	}
}
```

### Tags 模型

```php
/**
 * Tags
 * @Entity
 * @Table(name="tb_tags", id={"id"})
 * @property int $id
 * @property string $name
 */
class Tags extends Model
{
    // 省略其它……

	/**
     * 拥有本标签的文章列表
     * 
     * @PolymorphicToMany(model=ImiDemo\HttpDemo\MainServer\Model\Article::class, modelField="id", type="taggable_type", typeValue=1, field="taggable_id", middle="Taggables")
     * @JoinToMiddle(field="id", middleField="taggable_id")
     * @JoinFromMiddle(middleField="tag_id", field="id")
	 * 
     * @AutoSelect(false)
     *
     * @var \Imi\Util\ArrayList
     */
    protected $articles;

    /**
     * Get 拥有本标签的模型列表
     *
     * @return  \Imi\Util\ArrayList
     */ 
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Set 拥有本标签的模型列表
     *
     * @param  \Imi\Util\ArrayList  $articles  拥有本标签的模型列表
     *
     * @return  self
     */ 
    public function setArticles(\Imi\Util\ArrayList $articles)
    {
        $this->articles = $articles;

        return $this;
	}
	
	/**
     * 拥有本标签的书籍列表
     * 
     * @PolymorphicToMany(model=ImiDemo\HttpDemo\MainServer\Model\Book::class, modelField="id", type="taggable_type", typeValue=2, field="taggable_id", middle="Taggables")
     * @JoinToMiddle(field="id", middleField="taggable_id")
     * @JoinFromMiddle(middleField="tag_id", field="id")
	 * 
     * @AutoSelect(false)
     *
     * @var \Imi\Util\ArrayList
     */
    protected $books;

    /**
     * Get 拥有本标签的书籍列表
     *
     * @return  \Imi\Util\ArrayList
     */ 
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * Set 拥有本标签的书籍列表
     *
     * @param  \Imi\Util\ArrayList  $books  拥有本标签的书籍列表
     *
     * @return  self
     */ 
    public function setBooks(\Imi\Util\ArrayList $books)
    {
        $this->books = $books;

        return $this;
	}
	
}
```

### Taggables 模型

没有要设置的注解，不展示了

## 查询

> 常见的增删改查就不写了，和一对多关联一样用法

### 标签反查文章和书籍

```php
$tag = Tags::find(1);
$tag->queryRelations('articles', 'books');
$articles = $tag->articles;
$books = $tag->books;
```
