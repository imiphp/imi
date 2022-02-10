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
 * tb_member_role_relation 基类.
 *
 * @Entity(camel=true, bean=true)
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\MemberRoleRelation.name", default="tb_member_role_relation"), id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\MemberRoleRelation.poolName"))
 * @DDL(sql="CREATE TABLE `tb_member_role_relation` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `type` int(10) unsigned NOT NULL DEFAULT '0',   `member_id` int(10) unsigned NOT NULL,   `role_id` int(10) unsigned NOT NULL,   PRIMARY KEY (`id`),   KEY `member_id` (`member_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8", decode="")
 *
 * @property int|null $id
 * @property int|null $type
 * @property int|null $memberId
 * @property int|null $roleId
 */
abstract class MemberRoleRelationBase extends Model
{
    /**
     * id.
     *
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true, unsigned=true)
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
    public function setId($id)
    {
        $this->id = null === $id ? null : (int) $id;

        return $this;
    }

    /**
     * type.
     *
     * @Column(name="type", type="int", length=10, accuracy=0, nullable=false, default="0", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=true)
     */
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
    public function setType($type)
    {
        $this->type = null === $type ? null : (int) $type;

        return $this;
    }

    /**
     * member_id.
     *
     * @Column(name="member_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=true)
     */
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
    public function setMemberId($memberId)
    {
        $this->memberId = null === $memberId ? null : (int) $memberId;

        return $this;
    }

    /**
     * role_id.
     *
     * @Column(name="role_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=true)
     */
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
    public function setRoleId($roleId)
    {
        $this->roleId = null === $roleId ? null : (int) $roleId;

        return $this;
    }
}
