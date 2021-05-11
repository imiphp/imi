<?php

namespace Imi\AC\AccessControl;

use Imi\App;
use Imi\Bean\Traits\TAutoInject;

class Member
{
    use TAutoInject;

    /**
     * 用户 ID.
     *
     * @var int
     */
    private $memberId;

    /**
     * 角色列表.
     *
     * @var \Imi\AC\Model\Role[]
     */
    private $roles;

    /**
     * 支持的所有操作权限.
     *
     * @var \Imi\AC\Model\Operation[]
     */
    private $operations;

    /**
     * 用户服务层名称.
     *
     * @var string
     */
    protected $memberServiceBean = 'ACMemberService';

    /**
     * 操作权限服务层名称.
     *
     * @var string
     */
    protected $operationServiceBean = 'ACOperationService';

    /**
     * @var \Imi\AC\Service\MemberService
     */
    protected $memberService;

    /**
     * @var \Imi\AC\Service\OperationService
     */
    protected $operationService;

    /**
     * @param int $memberId
     */
    public function __construct($memberId)
    {
        $this->__autoInject();
        $this->memberService = App::getBean($this->memberServiceBean);
        $this->operationService = App::getBean($this->operationServiceBean);
        $this->memberId = $memberId;
        $this->updateRoles();
        $this->updateOperations();
    }

    /**
     * 处理角色的本地数据更新.
     *
     * @return void
     */
    private function updateRoles()
    {
        $roles = $this->memberService->getRoles($this->memberId);
        $this->roles = [];
        foreach ($roles as $role)
        {
            $this->roles[$role->code] = $role;
        }
    }

    /**
     * 处理操作的本地数据更新.
     *
     * @return void
     */
    private function updateOperations()
    {
        $operations = $this->memberService->getOperations($this->memberId);
        $this->operations = [];
        foreach ($operations as $operation)
        {
            $this->operations[$operation->code] = $operation;
        }
    }

    /**
     * 获取用户 ID.
     *
     * @return int
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * 获取该用户所有角色.
     *
     * @return \Imi\AC\Model\Role[]
     */
    public function getRoles()
    {
        return array_values($this->roles);
    }

    /**
     * 为用户增加角色.
     *
     * 传入角色代码
     *
     * @param string ...$roles
     *
     * @return void
     */
    public function addRoles(...$roles)
    {
        $this->memberService->addRoles($this->memberId, ...$roles);
        $this->updateRoles();
        $this->updateOperations();
    }

    /**
     * 为用户设置角色.
     *
     * 传入角色代码
     *
     * 调用后，用户只拥有本次传入的角色
     *
     * @param string ...$roles
     *
     * @return void
     */
    public function setRoles(...$roles)
    {
        $this->memberService->setRoles($this->memberId, ...$roles);
        $this->updateRoles();
        $this->updateOperations();
    }

    /**
     * 移除用户的角色.
     *
     * 传入角色代码
     *
     * @param string ...$roles
     *
     * @return void
     */
    public function removeRoles(...$roles)
    {
        $this->memberService->removeRoles($this->memberId, ...$roles);
        $this->updateRoles();
        $this->updateOperations();
    }

    /**
     * 根据角色代码判断，该用户是否拥有一个或多个角色.
     *
     * @param string ...$roles
     *
     * @return bool
     */
    public function hasRoles(...$roles)
    {
        foreach ($roles as $code)
        {
            if (!isset($this->roles[$code]))
            {
                return false;
            }
        }

        return true;
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
        $this->memberService->addOperations($this->memberId, ...$operations);
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
        $this->memberService->setOperations($this->memberId, ...$operations);
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
        $this->memberService->removeOperations($this->memberId, ...$operations);
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
