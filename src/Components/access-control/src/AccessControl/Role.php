<?php

declare(strict_types=1);

namespace Imi\AC\AccessControl;

use Imi\App;
use Imi\Bean\Traits\TAutoInject;

class Role
{
    use TAutoInject;

    /**
     * 角色代码
     *
     * @var int
     */
    private $roleCode;

    /**
     * 角色记录.
     *
     * @var \Imi\AC\Model\Role|null
     */
    private $roleInfo;

    /**
     * 支持的所有操作权限.
     *
     * @var \Imi\AC\Model\Operation[]
     */
    private $operations;

    /**
     * 角色服务层名称.
     *
     * @var string
     */
    protected $roleServiceBean = 'ACRoleService';

    /**
     * 操作权限服务层名称.
     *
     * @var string
     */
    protected $operationServiceBean = 'ACOperationService';

    /**
     * @var \Imi\AC\Service\RoleService
     */
    protected $roleService;

    /**
     * @var \Imi\AC\Service\OperationService
     */
    protected $operationService;

    /**
     * @param mixed  $pk
     * @param string $pkType
     */
    public function __construct($pk, $pkType = 'id')
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
     *
     * @return void
     */
    private function updateOperations()
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
     *
     * @return \Imi\AC\Model\Role
     */
    public function getRoleInfo()
    {
        return $this->roleInfo;
    }

    /**
     * 创建角色.
     *
     * @param string $name
     * @param string $code
     * @param string $description
     *
     * @return static|false
     */
    public static function create($name, $code = null, $description = '')
    {
        $record = App::getBean('ACRoleService')->create($name, $code, $description);
        if ($record)
        {
            return new static($record->code);
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
    public function getOperations()
    {
        return array_values($this->operations);
    }

    /**
     * 获取操作权限树.
     *
     * @return \Imi\AC\Model\Filter\OperationTreeItem[]
     */
    public function getOperationTree()
    {
        return $this->operationService->listToTree($this->operations);
    }

    /**
     * 增加操作权限.
     *
     * 传入操作代码
     *
     * @param string ...$operations
     *
     * @return void
     */
    public function addOperations(...$operations)
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
     *
     * @return void
     */
    public function setOperations(...$operations)
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
     *
     * @return void
     */
    public function removeOperations(...$operations)
    {
        $this->roleService->removeOperations($this->roleInfo->id, ...$operations);
        $this->updateOperations();
    }

    /**
     * 根据操作代码判断，是否拥有一个或多个操作权限.
     *
     * @param string ...$operations
     *
     * @return bool
     */
    public function hasOperations(...$operations)
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
