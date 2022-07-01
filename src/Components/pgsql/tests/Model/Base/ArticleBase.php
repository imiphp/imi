<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Config\Annotation\ConfigValue;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_article 基类.
 *
 * @Entity
 * @Table(name=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\Article.name", default="tb_article"), usePrefix=false, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\Article.poolName"))
 *
 * @property int|null    $id
 * @property string|null $title
 * @property string|null $content
 * @property string|null $time
 */
abstract class ArticleBase extends Model
{
    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEY = 'id';

    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEYS = ['id'];

    /**
     * id.

     *
     * @Column(name="id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=true, ndims=0, virtual=false)
     */
    protected ?int $id = null;

    /**
     * 获取 id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * 赋值 id.
     *
     * @param int|null $id id
     *
     * @return static
     */
    public function setId(?int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * title.

     *
     * @Column(name="title", type="varchar", length=0, accuracy=255, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
    protected ?string $title = null;

    /**
     * 获取 title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * 赋值 title.
     *
     * @param string|null $title title
     *
     * @return static
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * content.

     *
     * @Column(name="content", type="text", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
    protected ?string $content = null;

    /**
     * 获取 content.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * 赋值 content.
     *
     * @param string|null $content content
     *
     * @return static
     */
    public function setContent(?string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * time.

     *
     * @Column(name="time", type="timestamp", length=0, accuracy=2, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
    protected ?string $time = null;

    /**
     * 获取 time.
     */
    public function getTime(): ?string
    {
        return $this->time;
    }

    /**
     * 赋值 time.
     *
     * @param string|null $time time
     *
     * @return static
     */
    public function setTime(?string $time)
    {
        $this->time = $time;

        return $this;
    }
}
