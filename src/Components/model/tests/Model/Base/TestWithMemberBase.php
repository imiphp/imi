<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

/**
 * 测试 with member 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null $id
 * @property int|null $memberId
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_test_with_member', id: [
        'id',
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_test_with_member` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `member_id` int(10) unsigned NOT NULL,   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=\'测试 with member\'')
]
abstract class TestWithMemberBase extends Model
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
        \Imi\Model\Annotation\Column(name: 'id', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, isPrimaryKey: true, primaryKeyIndex: 0, isAutoIncrement: true, unsigned: true)
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
     * member_id.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'member_id', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, unsigned: true)
    ]
    protected ?int $memberId = null;

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
     *
     * @return static
     */
    public function setMemberId(mixed $memberId): self
    {
        $this->memberId = null === $memberId ? null : (int) $memberId;

        return $this;
    }
}
