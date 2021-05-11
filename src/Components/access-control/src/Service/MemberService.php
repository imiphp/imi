<?php

namespace Imi\AC\Service;

use Imi\AC\Exception\OperationNotFound;
use Imi\AC\Exception\RoleNotFound;
use Imi\AC\Model\MemberOperationRelation;
use Imi\AC\Model\MemberRoleRelation;
use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Db\Annotation\Transaction;

/**
 * @Bean("ACMemberService")
 */
class MemberService
{
    /**
     * 角色服务层名称.
     *
     * @var string
     */
    protected $roleServiceBean = 'ACRoleService';

    /**
     * 操作服务层名称.
     *
     * @var string
     */
    protected $operationServiceBean = 'ACOperationService';

    /**
     * 用户角色关联模型.
     *
     * @var string
     */
    protected $memberRoleRelationModel = MemberRoleRelation::class;

    /**
     * 用户操作权限关联模型.
     *
     * @var string
     */
    protected $memberOperationRelationModel = MemberOperationRelation::class;

    /**
     * @var \Imi\AC\Service\RoleService
     */
    protected $roleService;

    /**
     * @var \Imi\AC\Service\OperationService
     */
    protected $operationService;

    /**
     * @return void
     */
    public function __init()
    {
        $this->roleService = App::getBean($this->roleServiceBean);
        $this->operationService = App::getBean($this->operationServiceBean);
    }

    /**
     * 获取用户角色.
     *
     * @param int $memberId
     *
     * @return \Imi\AC\Model\Role[]
     */
    public function getRoles($memberId)
    {
        $roleIds = $this->memberRoleRelationModel::query()->where('member_id', '=', $memberId)
                                              ->field('role_id')
                                              ->select()
                                              ->getColumn();

        return $this->roleService->selectListByIds($roleIds);
    }

    /**
     * 增加角色.
     *
     * 传入角色代码
     *
     * @Transaction
     *
     * @param int    $memberId
     * @param string ...$roles
     *
     * @return void
     */
    public function addRoles($memberId, ...$roles)
    {
        foreach ($roles as $roleCode)
        {
            $role = $this->roleService->getByCode($roleCode);
            if (!$role)
            {
                throw new RoleNotFound(sprintf('Role code = %s does not found', $roleCode));
            }
            $relation = $this->memberRoleRelationModel::newInstance();
            $relation->memberId = $memberId;
            $relation->roleId = $role->id;
            $relation->save();
        }
    }

    /**
     * 设置角色.
     *
     * 传入角色代码
     *
     * 调用后，只拥有本次传入的角色
     *
     * @Transaction
     *
     * @param int    $memberId
     * @param string ...$roles
     *
     * @return void
     */
    public function setRoles($memberId, ...$roles)
    {
        $this->memberRoleRelationModel::query()->where('member_id', '=', $memberId)->delete();
        $this->addRoles($memberId, ...$roles);
    }

    /**
     * 移除角色.
     *
     * 传入角色代码
     *
     * @param int    $memberId
     * @param string ...$roles
     *
     * @return void
     */
    public function removeRoles($memberId, ...$roles)
    {
        $roleIds = $this->roleService->selectIdsByCodes($roles);
        if (!$roleIds)
        {
            return;
        }
        $this->memberRoleRelationModel::query()->where('member_id', '=', $memberId)
                                   ->whereIn('role_id', $roleIds)
                                   ->delete();
    }

    /**
     * 增加操作权限.
     *
     * 传入操作代码
     *
     * @Transaction
     *
     * @param int    $memberId
     * @param string ...$operations
     *
     * @return void
     */
    public function addOperations($memberId, ...$operations)
    {
        foreach ($operations as $operationCode)
        {
            $operation = $this->operationService->getByCode($operationCode);
            if (!$operation)
            {
                throw new OperationNotFound(sprintf('Operation code = %s does not found', $operationCode));
            }
            $relation = $this->memberOperationRelationModel::newInstance();
            $relation->memberId = $memberId;
            $relation->operationId = $operation->id;
            $relation->save();
        }
    }

    /**
     * 设置操作权限.
     *
     * 传入操作代码
     *
     * 调用后，只拥有本次传入的操作权限。不影响角色赋予的权限。
     *
     * @Transaction
     *
     * @param int    $memberId
     * @param string ...$operations
     *
     * @return void
     */
    public function setOperations($memberId, ...$operations)
    {
        $this->memberOperationRelationModel::query()->where('member_id', '=', $memberId)->delete();
        $this->addOperations($memberId, ...$operations);
    }

    /**
     * 获取支持的所有操作权限.
     *
     * @param int $memberId
     *
     * @return \Imi\AC\Model\Operation[]
     */
    public function getOperations($memberId)
    {
        $result = [];
        foreach (array_merge($this->getRoleOperations($memberId), $this->getOwnOperations($memberId)) as $operation)
        {
            $result[$operation->code] = $operation;
        }

        return array_values($result);
    }

    /**
     * 获取角色授予当前用户的权限.
     *
     * @param int $memberId
     *
     * @return \Imi\AC\Model\Operation[]
     */
    public function getRoleOperations($memberId)
    {
        $roles = $this->getRoles($memberId);
        $result = [];
        foreach ($roles as $role)
        {
            $operations = $this->roleService->getOperations($role->id);
            foreach ($operations as $operation)
            {
                $result[$operation->code] = $operation;
            }
        }

        return array_values($result);
    }

    /**
     * 获取当前用户单独被授予的权限.
     *
     * @param int $memberId
     *
     * @return \Imi\AC\Model\Operation[]
     */
    public function getOwnOperations($memberId)
    {
        $operationIds = $this->memberOperationRelationModel::query()->where('member_id', '=', $memberId)
                                                        ->field('operation_id')
                                                        ->select()
                                                        ->getColumn();

        return $this->operationService->selectListByIds($operationIds);
    }

    /**
     * 移除操作权限.
     *
     * 传入操作代码
     *
     * @param int    $memberId
     * @param string ...$operations
     *
     * @return void
     */
    public function removeOperations($memberId, ...$operations)
    {
        $operationIds = $this->operationService->selectIdsByCodes($operations);
        if (!$operationIds)
        {
            return;
        }
        $this->memberOperationRelationModel::query()->where('member_id', '=', $memberId)
                                        ->whereIn('operation_id', $operationIds)
                                        ->delete();
    }
}
