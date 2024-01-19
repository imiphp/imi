<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Id;
use Imi\Model\Annotation\Serializable;
use Imi\Model\Annotation\Table;
use Imi\Model\IdGenerator\UUIDGeneratorType;
use Imi\Model\Model;

/**
 * Article.
 *
 * @property int|null    $id
 * @property int|null    $memberId
 * @property string|null $title
 * @property string|null $content
 * @property string|null $time
 */
#[Entity]
#[Table(name: 'tb_article', id: ['id'])]
#[DDL(sql: 'CREATE TABLE `tb_article` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `member_id` int(10) unsigned NOT NULL DEFAULT \'0\',   `title` varchar(255) NOT NULL,   `content` mediumtext NOT NULL,   `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,   PRIMARY KEY (`id`) USING BTREE,   KEY `member_id` (`member_id`) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT=\'@article\'', decode: '')]
class ArticleId extends Model
{
    /**
     * id.
     */
    #[Column(name: 'id', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, default: '', isPrimaryKey: true, primaryKeyIndex: 0, isAutoIncrement: true, unsigned: true)]
    #[Id]
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
     */
    public function setId(?int $id): static
    {
        $this->id = null === $id ? null : (int) $id;

        return $this;
    }

    /**
     * member_id.
     */
    #[Column(name: 'member_id', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, default: '0', unsigned: true)]
    protected ?int $memberId = 0;

    /**
     * 获取 memberId.
     */
    public function getMemberId(): ?int
    {
        return $this->memberId;
    }

    /**
     * 赋值 memberId.
     *
     * @param int|null $memberId member_id
     */
    public function setMemberId(?int $memberId): static
    {
        $this->memberId = null === $memberId ? null : (int) $memberId;

        return $this;
    }

    /**
     * title.
     */
    #[Column(name: 'title', type: 'varchar', length: 255, nullable: false, default: '')]
    #[Id(index: false, generator: 'Imi\\Model\\IdGenerator\\UUIDGenerator')]
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
     */
    public function setTitle(?string $title): static
    {
        if (\is_string($title) && mb_strlen($title) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $title is 255');
        }
        $this->title = null === $title ? null : (string) $title;

        return $this;
    }

    /**
     * content.
     */
    #[Column(name: 'content', type: 'mediumtext', length: 0, nullable: false, default: '')]
    #[Id(index: false, generator: 'Imi\\Model\\IdGenerator\\UUIDGenerator', generatorOptions: ['type' => UUIDGeneratorType::SHA1, 'ns' => '99e4edaf-8363-466e-bddf-7254db57675c', 'nameField' => 'title'])]
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
     */
    public function setContent(?string $content): static
    {
        if (\is_string($content) && mb_strlen($content) > 16777215)
        {
            throw new \InvalidArgumentException('The maximum length of $content is 16777215');
        }
        $this->content = null === $content ? null : (string) $content;

        return $this;
    }

    /**
     * time.
     */
    #[Column(name: 'time', type: 'timestamp', length: 0, nullable: false, default: 'CURRENT_TIMESTAMP')]
    #[Serializable(allow: false)]
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
     */
    public function setTime(?string $time): static
    {
        $this->time = null === $time ? null : (string) $time;

        return $this;
    }
}
