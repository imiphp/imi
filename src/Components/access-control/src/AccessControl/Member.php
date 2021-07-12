<?php

declare(strict_types=1);

namespace Imi\AC\AccessControl;

use Imi\AC\Service\MemberService;
use Imi\AC\Service\OperationService;
use Imi\App;
use Imi\Bean\Traits\TAutoInject;

class Member
{
    use TAutoInject;

    /**
     * 用户 ID.
     */
    private int $memberId;

    /**
     * 角色列表.
     *
     * @var \Imi\AC\Model\Role[]
     */
    private array $roles;

    /**
     * 支持的所有操作权限.
     *
     * @var \Imi\AC\Model\Operation[]
     */
    private array $operations;

    /**
     * 用户服务层名称.
     */
    protected string $memberServiceBean = 'ACMemberService';

    /**
     * 操作权限服务层名称.
     */
    protected string $operationServiceBean = 'ACOperationService';

    protected MemberService $memberService;

    protected OperationService $operationService;

    public function __construct(int $memberId)
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
     */
    private function updateRoles(): void
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
     */
    private function updateOperations(): void
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
     */
    public function getMemberId(): int
    {
        return $this->memberId;
    }

    /**
     * 获取该用户所有角色.
     *
     * @return \Imi\AC\Model\Role[]
     */
    public function getRoles(): array
    {
        return array_values($this->roles);
    }

    /**
     * 为用户增加角色.
     *
     * 传入角色代码
     *
     * @param string ...$roles
     */
    public function addRoles(string ...$roles): void
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
     */
    public function setRoles(string ...$roles): void
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
     */
    public function removeRoles(string ...$roles): void
    {
        $this->memberService->removeRoles($this->memberId, ...$roles);
        $this->updateRoles();
        $this->updateOperations();
    }

    /**
     * 根据角色代码判断，该用户是否拥有一个或多个角色.
     *
     * @param string ...$roles
     */
    public function hasRoles(string ...$roles): bool
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
     */
    public function setOperations(string ...$operations): void
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
     */
    public function removeOperations(string ...$operations): void
    {
        $this->memberService->removeOperations($this->memberId, ...$operations);
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
