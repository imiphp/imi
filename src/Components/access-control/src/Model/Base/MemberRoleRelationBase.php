<?php

declare(strict_types=1);

namespace Imi\AC\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * ac_member_role_relation 基类.
 *
 * @Entity
 * @Table(name="ac_member_role_relation", id={"member_id", "role_id"})
 * @DDL("CREATE TABLE `ac_member_role_relation` (   `member_id` int(10) unsigned NOT NULL COMMENT '用户ID',   `role_id` int(10) unsigned NOT NULL COMMENT '角色ID',   PRIMARY KEY (`member_id`,`role_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8")
 *
 * @property int|null $memberId 用户ID
 * @property int|null $roleId   角色ID
 */
abstract class MemberRoleRelationBase extends Model
{
    /**
     * 用户ID
     * member_id.
     *
     * @Column(name="member_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=false)
     */
    protected ?int $memberId = null;

    /**
     * 获取 memberId - 用户ID.
     */
    public function getMemberId(): ?int
    {
        return $this->memberId;
    }

    /**
     * 赋值 memberId - 用户ID.
     *
     * @param int|null $memberId member_id
     *
     * @return static
     */
    public function setMemberId(?int $memberId)
    {
        $this->memberId = $memberId;

        return $this;
    }

    /**
     * 角色ID
     * role_id.
     *
     * @Column(name="role_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=false)
     */
    protected ?int $roleId = null;

    /**
     * 获取 roleId - 角色ID.
     */
    public function getRoleId(): ?int
    {
        return $this->roleId;
    }

    /**
     * 赋值 roleId - 角色ID.
     *
     * @param int|null $roleId role_id
     *
     * @return static
     */
    public function setRoleId(?int $roleId)
    {
        $this->roleId = $roleId;

        return $this;
    }
}
