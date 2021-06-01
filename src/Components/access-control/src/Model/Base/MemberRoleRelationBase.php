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
 * @DDL("CREATE TABLE `ac_member_role_relation` (
) ENGINE=InnoDB DEFAULT CHARSET=utf8")
 * @property int $memberId 用户ID
 * @property int $roleId   角色ID
 */
abstract class MemberRoleRelationBase extends Model
{
    /**
     * 用户ID
     * member_id.
     *
     * @Column(name="member_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=false)
     *
     * @var int
     */
    protected $memberId;

    /**
     * 获取 memberId - 用户ID.
     *
     * @return int
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * 赋值 memberId - 用户ID.
     *
     * @param int $memberId member_id
     *
     * @return static
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;

        return $this;
    }

    /**
     * 角色ID
     * role_id.
     *
     * @Column(name="role_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=false)
     *
     * @var int
     */
    protected $roleId;

    /**
     * 获取 roleId - 角色ID.
     *
     * @return int
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * 赋值 roleId - 角色ID.
     *
     * @param int $roleId role_id
     *
     * @return static
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;

        return $this;
    }
}
