<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Relation\AutoSave;
use Imi\Model\Annotation\Relation\JoinFromMiddle;
use Imi\Model\Annotation\Relation\JoinToMiddle;
use Imi\Model\Annotation\Relation\ManyToMany;

/**
 * Member.
 *
 * @property MemberRoleRelation[]|null $roleRelations
 * @property Role[]|null               $roles
 * @property MemberRoleRelation[]|null $roleRelationsWith
 * @property Role[]|null               $rolesWith
 */
#[Inherit]
class MemberWithRoles extends Member
{
    /**
     * @var MemberRoleRelation[]|null
     */
    #[ManyToMany(model: 'Role', middle: 'MemberRoleRelation', rightMany: 'roles')]
    #[JoinToMiddle(field: 'id', middleField: 'member_id')]
    #[JoinFromMiddle(middleField: 'role_id', field: 'id')]
    #[AutoSave]
    protected $roleRelations = null;

    /**
     * Get the value of roleRelation.
     *
     * @return MemberRoleRelation[]|null
     */
    public function getRoleRelations(): ?array
    {
        return $this->roleRelations;
    }

    /**
     * Set the value of roleRelation.
     *
     * @param MemberRoleRelation[]|null $roleRelations
     */
    public function setRoleRelations(?array $roleRelations): self
    {
        $this->roleRelations = $roleRelations;

        return $this;
    }

    /**
     * @var Role[]|null
     */
    #[Column(virtual: true)]
    protected $roles;

    /**
     * Get the value of roles.
     *
     * @return Role[]|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * Set the value of roles.
     *
     * @param Role[]|null $roles
     */
    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @var MemberRoleRelation[]|null
     */
    #[ManyToMany(model: 'Role', middle: 'MemberRoleRelation', rightMany: 'rolesWith', with: true)]
    #[JoinToMiddle(field: 'id', middleField: 'member_id')]
    #[JoinFromMiddle(middleField: 'role_id', field: 'id')]
    #[AutoSave]
    protected $roleRelationsWith = null;

    /**
     * Get the value of roleRelation.
     *
     * @return MemberRoleRelation[]|null
     */
    public function getRoleRelationsWith(): ?array
    {
        return $this->roleRelationsWith;
    }

    /**
     * Set the value of roleRelation.
     *
     * @param MemberRoleRelation[]|null $roleRelationsWith
     */
    public function setRoleRelationsWith(?array $roleRelationsWith): self
    {
        $this->roleRelationsWith = $roleRelationsWith;

        return $this;
    }

    /**
     * @var Role[]|null
     */
    #[Column(virtual: true)]
    protected $rolesWith;

    /**
     * Get the value of rolesWith.
     *
     * @return Role[]|null
     */
    public function getRolesWith(): ?array
    {
        return $this->rolesWith;
    }

    /**
     * Set the value of rolesWith.
     *
     * @param Role[]|null $rolesWith
     */
    public function setRolesWith(?array $rolesWith): self
    {
        $this->rolesWith = $rolesWith;

        return $this;
    }
}
