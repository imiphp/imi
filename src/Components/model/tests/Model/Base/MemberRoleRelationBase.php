<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

/**
 * tb_member_role_relation 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null $id
 * @property int|null $type
 * @property int|null $memberId
 * @property int|null $roleId
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_member_role_relation', id: [
        'id',
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_member_role_relation` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `type` int(10) unsigned NOT NULL DEFAULT \'0\',   `member_id` int(10) unsigned NOT NULL,   `role_id` int(10) unsigned NOT NULL,   PRIMARY KEY (`id`),   KEY `member_id` (`member_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8')
]
abstract class MemberRoleRelationBase extends Model
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
     * type.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'type', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, default: '0', unsigned: true)
    ]
    protected ?int $type = 0;

    /**
     * 获取 type.
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * 赋值 type.
     *
     * @param int|null $type type
     *
     * @return static
     */
    public function setType(mixed $type): self
    {
        $this->type = null === $type ? null : (int) $type;

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

    /**
     * role_id.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'role_id', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, unsigned: true)
    ]
    protected ?int $roleId = null;

    /**
     * 获取 roleId.
     */
    public function getRoleId(): ?int
    {
        return $this->roleId;
    }

    /**
     * 赋值 roleId.
     *
     * @param int|null $roleId role_id
     *
     * @return static
     */
    public function setRoleId(mixed $roleId): self
    {
        $this->roleId = null === $roleId ? null : (int) $roleId;

        return $this;
    }
}
