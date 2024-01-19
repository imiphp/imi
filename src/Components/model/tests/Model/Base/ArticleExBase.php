<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

/**
 * tb_article_ex 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null                                    $articleId
 * @property \Imi\Util\LazyArrayObject|object|array|null $data
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_article_ex', id: [
        'article_id',
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_article_ex` (   `article_id` int(10) unsigned NOT NULL,   `data` json DEFAULT NULL,   PRIMARY KEY (`article_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8')
]
abstract class ArticleExBase extends Model
{
    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEY = 'article_id';

    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEYS = ['article_id'];

    /**
     * article_id.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'article_id', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, isPrimaryKey: true, primaryKeyIndex: 0, unsigned: true)
    ]
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
    public function setArticleId(mixed $articleId): self
    {
        $this->articleId = null === $articleId ? null : (int) $articleId;

        return $this;
    }

    /**
     * data.
     *
     * @var \Imi\Util\LazyArrayObject|object|array|null
     */
    #[
        \Imi\Model\Annotation\Column(name: 'data', type: 'json', length: 0)
    ]
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
    public function setData(mixed $data): self
    {
        $this->data = null === $data ? null : $data;

        return $this;
    }
}
