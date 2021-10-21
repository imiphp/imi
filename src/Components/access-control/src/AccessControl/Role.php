<?php

declare(strict_types=1);

namespace Imi\AC\AccessControl;

use Imi\AC\Service\OperationService;
use Imi\AC\Service\RoleService;
use Imi\App;
use Imi\Bean\Traits\TAutoInject;

class Role
{
    use TAutoInject;

    /**
     * 角色代码
     */
    private int $roleCode;

    /**
     * 角色记录.
     */
    private ?\Imi\AC\Model\Role $roleInfo;

    /**
     * 支持的所有操作权限.
     *
     * @var \Imi\AC\Model\Operation[]
     */
    private array $operations;

    /**
     * 角色服务层名称.
     */
    protected string $roleServiceBean = 'ACRoleService';

    /**
     * 操作权限服务层名称.
     */
    protected string $operationServiceBean = 'ACOperationService';

    protected RoleService $roleService;

    protected OperationService $operationService;

    /**
     * @param mixed $pk
     */
    public function __construct($pk, string $pkType = 'id')
    {
        $this->__autoInject();
        $this->roleService = App::getBean($this->roleServiceBean);
        $this->operationService = App::getBean($this->operationServiceBean);
        switch ($pkType)
        {
            case 'id':
                $this->roleInfo = $this->roleService->get($pk);
                if ($this->roleInfo)
                {
                    $this->roleCode = $this->roleInfo->code;
                }
                break;
            case 'code':
                $this->roleCode = $pk;
                $this->roleInfo = $this->roleService->getByCode($pk);
                break;
        }
        if ($this->roleInfo)
        {
            $this->updateOperations();
        }
    }

    /**
     * 处理操作的本地数据更新.
     */
    private function updateOperations(): void
    {
        $operations = $this->roleService->getOperations($this->roleInfo->id);
        $this->operations = [];
        foreach ($operations as $operation)
        {
            $this->operations[$operation->code] = $operation;
        }
    }

    /**
     * 获取角色记录.
     */
    public function getRoleInfo(): \Imi\AC\Model\Role
    {
        return $this->roleInfo;
    }

    /**
     * 创建角色.
     *
     * @return static|false
     */
    public static function create(string $name, ?string $code = null, string $description = '')
    {
        $record = App::getBean('ACRoleService')->create($name, $code, $description);
        if ($record)
        {
            return new static($record->code, 'code');
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取支持的所有操作权限.
     *
     * @return \Imi\AC\Model\Operation[]
     */
    public function getOperations(): array
    {
        return array_values($this->operations);
    }

    /**
     * 获取操作权限树.
     *
     * @return \Imi\AC\Model\Filter\OperationTreeItem[]
     */
    public function getOperationTree(): array
    {
        return $this->operationService->listToTree($this->operations);
    }

    /**
     * 增加操作权限.
     *
     * 传入操作代码
     *
     * @param string ...$operations
     */
    public function addOperations(string ...$operations): void
    {
        $this->roleService->addOperations($this->roleInfo->id, ...$operations);
        $this->updateOperations();
    }

    /**
     * 设置操作权限.
     *
     * 传入操作代码
     *
     * 调用后，只拥有本次传入的操作权限
     *
     * @param string ...$operations
     */
    public function setOperations(string ...$operations): void
    {
        $this->roleService->setOperations($this->roleInfo->id, ...$operations);
        $this->updateOperations();
    }

    /**
     * 移除操作权限.
     *
     * 传入操作代码
     *
     * @param string ...$operations
     */
    public function removeOperations(string ...$operations): void
    {
        $this->roleService->removeOperations($this->roleInfo->id, ...$operations);
        $this->updateOperations();
    }

    /**
     * 根据操作代码判断，是否拥有一个或多个操作权限.
     *
     * @param string ...$operations
     */
    public function hasOperations(string ...$operations): bool
    {
        foreach ($operations as $code)
        {
            if (!isset($this->operations[$code]))
            {
                return false;
            }
        }

        return true;
    }
}
