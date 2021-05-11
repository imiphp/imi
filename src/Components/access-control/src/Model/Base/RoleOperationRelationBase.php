<?php

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
 * @DDL("CREATE TABLE `ac_role_operation_relation` (
) ENGINE=InnoDB DEFAULT CHARSET=utf8")
 * @property int $roleId      角色ID
 * @property int $operationId 操作ID
 */
abstract class RoleOperationRelationBase extends Model
{
    /**
     * 角色ID
     * role_id.
     *
     * @Column(name="role_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=false)
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

    /**
     * 操作ID
     * operation_id.
     *
     * @Column(name="operation_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=false)
     *
     * @var int
     */
    protected $operationId;

    /**
     * 获取 operationId - 操作ID.
     *
     * @return int
     */
    public function getOperationId()
    {
        return $this->operationId;
    }

    /**
     * 赋值 operationId - 操作ID.
     *
     * @param int $operationId operation_id
     *
     * @return static
     */
    public function setOperationId($operationId)
    {
        $this->operationId = $operationId;

        return $this;
    }
}
