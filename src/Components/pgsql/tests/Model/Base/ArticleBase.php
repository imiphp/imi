<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_article 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null    $id
 * @property string|null $title
 * @property string|null $content
 * @property string|null $time
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_article', id: [
        'id',
    ])
]
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
     */
    #[
        \Imi\Model\Annotation\Column(name: 'id', type: 'int4', nullable: false, isPrimaryKey: true, primaryKeyIndex: 0, isAutoIncrement: true)
    ]
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
    public function setId(mixed $id): self
    {
        $this->id = null === $id ? null : (int) $id;

        return $this;
    }

    /**
     * title.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'title', type: 'varchar', length: 255, nullable: false)
    ]
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
    public function setTitle(mixed $title): self
    {
        if (\is_string($title) && mb_strlen($title) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $title is 255');
        }
        $this->title = null === $title ? null : $title;

        return $this;
    }

    /**
     * content.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'content', type: 'text', nullable: false)
    ]
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
    public function setContent(mixed $content): self
    {
        $this->content = null === $content ? null : $content;

        return $this;
    }

    /**
     * time.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'time', type: 'timestamp', length: 6, nullable: false)
    ]
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
    public function setTime(mixed $time): self
    {
        $this->time = null === $time ? null : $time;

        return $this;
    }
}
