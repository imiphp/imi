<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model\Base;

use Imi\Config\Annotation\ConfigValue;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * tb_article_ex 基类.
 *
 * @Entity
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\ArticleEx.name", default="tb_article_ex"), id={"article_id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\ArticleEx.poolName"))
 * @DDL(sql="CREATE TABLE `tb_article_ex` (   `article_id` int(10) unsigned NOT NULL,   `data` json DEFAULT NULL,   PRIMARY KEY (`article_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8", decode="")
 *
 * @property int|null                                    $articleId
 * @property \Imi\Util\LazyArrayObject|object|array|null $data
 */
abstract class ArticleExBase extends Model
{
    /**
     * article_id.
     *
     * @Column(name="article_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=false)
     */
    protected ?int $articleId = null;

    /**
     * 获取 articleId.
     */
    public function getArticleId(): ?int
    {
        return $this->articleId;
    }

    /**
     * 赋值 articleId.
     *
     * @param int|null $articleId article_id
     *
     * @return static
     */
    public function setArticleId($articleId)
    {
        $this->articleId = null === $articleId ? null : (int) $articleId;

        return $this;
    }

    /**
     * data.
     *
     * @Column(name="data", type="json", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var \Imi\Util\LazyArrayObject|object|array|null
     */
    protected $data = null;

    /**
     * 获取 data.
     *
     * @return \Imi\Util\LazyArrayObject|object|array|null
     */
    public function &getData()
    {
        return $this->data;
    }

    /**
     * 赋值 data.
     *
     * @param \Imi\Util\LazyArrayObject|object|array|null $data data
     *
     * @return static
     */
    public function setData($data)
    {
        $this->data = null === $data ? null : $data;

        return $this;
    }
}
