<?php

declare(strict_types=1);

namespace Imi\AC\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * ac_role_operation_relation 基类.
 *
 * @Entity
 * @Table(name="ac_role_operation_relation", id={"role_id", "operation_id"})
 * @DDL("CREATE TABLE `ac_role_operation_relation` (   `role_id` int(10) unsigned NOT NULL COMMENT '角色ID',   `operation_id` int(10) unsigned NOT NULL COMMENT '操作ID',   PRIMARY KEY (`role_id`,`operation_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8")
 *
 * @property int|null $roleId      角色ID
 * @property int|null $operationId 操作ID
 */
abstract class RoleOperationRelationBase extends Model
{
    /**
     * 角色ID
     * role_id.
     *
     * @Column(name="role_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=false)
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

    /**
     * 操作ID
     * operation_id.
     *
     * @Column(name="operation_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=false)
     */
    protected ?int $operationId = null;

    /**
     * 获取 operationId - 操作ID.
     */
    public function getOperationId(): ?int
    {
        return $this->operationId;
    }

    /**
     * 赋值 operationId - 操作ID.
     *
     * @param int|null $operationId operation_id
     *
     * @return static
     */
    public function setOperationId(?int $operationId)
    {
        $this->operationId = $operationId;

        return $this;
    }
}
