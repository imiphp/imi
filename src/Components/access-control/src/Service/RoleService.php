<?php

declare(strict_types=1);

namespace Imi\AC\Service;

use Imi\AC\Exception\OperationNotFound;
use Imi\AC\Exception\RoleNotFound;
use Imi\AC\Model\Role;
use Imi\AC\Model\RoleOperationRelation;
use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Db\Annotation\Transaction;

/**
 * @Bean("ACRoleService")
 */
class RoleService
{
    /**
     * 角色权限关联模型.
     */
    protected string $roleOperationRelationModel = RoleOperationRelation::class;

    /**
     * 角色模型.
     */
    protected string $roleModel = Role::class;

    /**
     * 操作权限服务层名称.
     */
    protected string $operationServiceBean = 'ACOperationService';

    /**
     * @var \Imi\AC\Service\OperationService
     */
    protected OperationService $operationService;

    public function __init(): void
    {
        $this->operationService = App::getBean($this->operationServiceBean);
    }

    /**
     * 获取角色.
     */
    public function get(int $id): ?Role
    {
        return $this->roleModel::find($id);
    }

    /**
     * 根据代码获取角色.
     */
    public function getByCode(string $code): ?Role
    {
        return $this->roleModel::query()->where('code', '=', $code)->select()->get();
    }

    /**
     * 根据id列表查询记录.
     *
     * @return \Imi\AC\Model\Role[]
     */
    public function selectListByIds(array $ids): array
    {
        if (!$ids)
        {
            return [];
        }

        return $this->roleModel::query()->whereIn('id', $ids)
                            ->select()
                            ->getArray();
    }

    /**
     * 根据多个角色获取操作ID.
     *
     * @return int[]
     */
    public function selectIdsByCodes(array $codes): array
    {
        if (!$codes)
        {
            return [];
        }

        return $this->roleModel::query()->field('id')->whereIn('code', $codes)->select()->getColumn();
    }

    /**
     * 查询列表.
     *
     * @return \Imi\AC\Model\Role[]
     */
    public function selectList(): array
    {
        return $this->roleModel::select();
    }

    /**
     * 创建角色.
     *
     * @return \Imi\AC\Model\Role|false
     */
    public function create(string $name, ?string $code = null, string $description = '')
    {
        $record = $this->roleModel::newInstance();
        $record->name = $name;
        $record->code = $code ?? $name;
        $record->description = $description;
        $result = $record->insert();
        if (!$result->isSuccess())
        {
            return false;
        }

        return $record;
    }

    /**
     * 更新角色.
     */
    public function update(int $id, string $name, ?string $code, string $description = ''): bool
    {
        $record = $this->get($id);
        if (!$record)
        {
            throw new RoleNotFound(sprintf('Role id = %s does not found', $id));
        }
        $record->name = $name;
        $record->code = $code;
        $record->description = $description;

        return $record->update()->isSuccess();
    }

    /**
     * 删除角色.
     */
    public function delete(int $id): bool
    {
        $record = $this->get($id);
        if (!$record)
        {
            throw new RoleNotFound(sprintf('Role id = %s does not found', $id));
        }

        return $record->delete()->isSuccess();
    }

    /**
     * 增加操作权限.
     *
     * 传入操作代码
     *
     * @Transaction
     *
     * @param string ...$operations
     */
    public function addOperations(int $roleId, string ...$operations): void
    {
        foreach ($operations as $operationCode)
        {
            $operation = $this->operationService->getByCode($operationCode);
            if (!$operation)
            {
                throw new OperationNotFound(sprintf('Operation code = %s does not found', $operationCode));
            }
            $relation = $this->roleOperationRelationModel::newInstance();
            $relation->roleId = $roleId;
            $relation->operationId = $operation->id;
            $relation->save();
        }
    }

    /**
     * 设置操作权限.
     *
     * 传入操作代码
     *
     * 调用后，只拥有本次传入的操作权限
     *
     * @Transaction
     *
     * @param string ...$operations
     */
    public function setOperations(int $roleId, string ...$operations): void
    {
        $this->roleOperationRelationModel::query()->where('role_id', '=', $roleId)->delete();
        $this->addOperations($roleId, ...$operations);
    }

    /**
     * 获取支持的所有操作权限.
     *
     * @return \Imi\AC\Model\Operation[]
     */
    public function getOperations(int $roleId): array
    {
        $operationIds = $this->roleOperationRelationModel::query()->where('role_id', '=', $roleId)
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
     * @param string ...$operations
     */
    public function removeOperations(int $roleId, string ...$operations): void
    {
        $operationIds = $this->operationService->selectIdsByCodes($operations);
        if (!$operationIds)
        {
            return;
        }
        $this->roleOperationRelationModel::query()->where('role_id', '=', $roleId)
                                      ->whereIn('operation_id', $operationIds)
                                      ->delete();
    }
}
