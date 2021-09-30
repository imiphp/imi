<?php

declare(strict_types=1);

namespace Imi\AC\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * ac_member_operation_relation 基类.
 *
 * @Entity
 * @Table(name="ac_member_operation_relation", id={"member_id", "operation_id"})
 * @DDL("CREATE TABLE `ac_member_operation_relation` ( `member_id` INT(10) UNSIGNED NOT NULL COMMENT '用户ID', `operation_id` INT(10) UNSIGNED NOT NULL COMMENT '操作ID', PRIMARY KEY(`member_id`, `operation_id`) USING BTREE) ENGINE=InnoDB DEFAULT CHARSET=utf8")
 * @property int $memberId    用户ID
 * @property int $operationId 操作ID
 */
abstract class MemberOperationRelationBase extends Model
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
