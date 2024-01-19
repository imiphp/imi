<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Relation\JoinFrom;
use Imi\Model\Annotation\Relation\JoinFromMiddle;
use Imi\Model\Annotation\Relation\JoinTo;
use Imi\Model\Annotation\Relation\JoinToMiddle;
use Imi\Model\Annotation\Relation\PolymorphicManyToMany;
use Imi\Model\Annotation\Relation\PolymorphicOneToMany;
use Imi\Model\Annotation\Relation\PolymorphicOneToOne;
use Imi\Model\Annotation\Relation\PolymorphicToMany;
use Imi\Model\Annotation\Relation\PolymorphicToOne;
use Imi\Model\Test\Model\Base\PolymorphicBase;

/**
 * tb_polymorphic.
 */
#[Inherit]
class Polymorphic extends PolymorphicBase
{
    #[PolymorphicOneToOne(model: 'Article', type: 'title', typeValue: 'PolymorphicOneToOne')]
    #[JoinFrom(field: 'one_to_one')]
    #[JoinTo(field: 'member_id')]
    protected ?Article $oneToOneResult = null;

    /**
     * Get the value of oneToOneResult.
     */
    public function getOneToOneResult(): ?Article
    {
        return $this->oneToOneResult;
    }

    /**
     * Set the value of oneToOneResult.
     */
    public function setOneToOneResult(?Article $oneToOneResult): self
    {
        $this->oneToOneResult = $oneToOneResult;

        return $this;
    }

    /**
     * @var Article[]|null
     */
    #[PolymorphicOneToMany(model: 'Article', type: 'title', typeValue: 'PolymorphicOneToMany')]
    #[JoinFrom(field: 'one_to_many')]
    #[JoinTo(field: 'member_id')]
    protected ?iterable $oneToManyResult = null;

    /**
     * Get the value of oneToManyResult.
     *
     * @return Article[]|null
     */
    public function getOneToManyResult(): ?iterable
    {
        return $this->oneToManyResult;
    }

    /**
     * Set the value of oneToManyResult.
     *
     * @param Article[]|null $oneToManyResult
     */
    public function setOneToManyResult(?iterable $oneToManyResult): self
    {
        $this->oneToManyResult = $oneToManyResult;

        return $this;
    }

    /**
     * @var MemberRoleRelation[]|null
     */
    #[PolymorphicManyToMany(model: 'Role', middle: 'MemberRoleRelation', rightMany: 'manyToManyResultList', type: 'type', typeValue: 1)]
    #[JoinToMiddle(field: 'many_to_many', middleField: 'member_id')]
    #[JoinFromMiddle(middleField: 'role_id', field: 'id')]
    protected ?iterable $manyToManyResult = null;

    /**
     * Get the value of manyToManyResult.
     *
     * @return MemberRoleRelation[]
     */
    public function getManyToManyResult(): ?iterable
    {
        return $this->manyToManyResult;
    }

    /**
     * Set the value of manyToManyResult.
     *
     * @param MemberRoleRelation[] $manyToManyResult
     */
    public function setManyToManyResult(?iterable $manyToManyResult): self
    {
        $this->manyToManyResult = $manyToManyResult;

        return $this;
    }

    /**
     * @var Role[]|null
     */
    #[Column(virtual: true)]
    protected ?iterable $manyToManyResultList = null;

    /**
     * Get the value of manyToManyResultList.
     *
     * @return Role[]|null
     */
    public function getManyToManyResultList(): ?iterable
    {
        return $this->manyToManyResultList;
    }

    /**
     * Set the value of manyToManyResultList.
     *
     * @param Role[]|null $manyToManyResultList
     */
    public function setManyToManyResultList(?iterable $manyToManyResultList): self
    {
        $this->manyToManyResultList = $manyToManyResultList;

        return $this;
    }

    #[PolymorphicToOne(model: 'Member', modelField: 'id', type: 'type', typeValue: 1, field: 'to_one')]
    #[PolymorphicToOne(model: 'Article', modelField: 'id', type: 'type', typeValue: 2, field: 'to_one')]
    protected Member|Article|null $toOneResult = null;

    /**
     * Get the value of toOneResult.
     */
    public function getToOneResult(): Member|Article|null
    {
        return $this->toOneResult;
    }

    /**
     * Set the value of toOneResult.
     */
    public function setToOneResult(Member|Article|null $toOneResult): self
    {
        $this->toOneResult = $toOneResult;

        return $this;
    }

    /**
     * @var Role[]|null
     */
    #[PolymorphicToMany(model: 'Role', modelField: 'id', type: 'type', typeValue: 6, field: 'to_many', middle: 'MemberRoleRelation', middleLeftField: 'role_id', middleRightField: 'member_id')]
    protected ?iterable $toManyResult = null;

    /**
     * Get the value of toManyResult.
     *
     * @return Role[]|null
     */
    public function getToManyResult(): ?iterable
    {
        return $this->toManyResult;
    }

    /**
     * Set the value of toManyResult.
     *
     * @param Role[]|null $toManyResult
     */
    public function setToManyResult(?iterable $toManyResult): self
    {
        $this->toManyResult = $toManyResult;

        return $this;
    }

    #[PolymorphicOneToOne(model: 'Article', type: 'title', typeValue: 'PolymorphicOneToOne', with: true)]
    #[JoinFrom(field: 'one_to_one')]
    #[JoinTo(field: 'member_id')]
    protected ?Article $oneToOneResultWith = null;

    /**
     * Get the value of oneToOneResultWith.
     */
    public function getOneToOneResultWith(): ?Article
    {
        return $this->oneToOneResultWith;
    }

    /**
     * Set the value of oneToOneResultWith.
     */
    public function setOneToOneResultWith(?Article $oneToOneResultWith): self
    {
        $this->oneToOneResultWith = $oneToOneResultWith;

        return $this;
    }

    /**
     * @var Article[]|null
     */
    #[PolymorphicOneToMany(model: 'Article', type: 'title', typeValue: 'PolymorphicOneToMany', with: true)]
    #[JoinFrom(field: 'one_to_many')]
    #[JoinTo(field: 'member_id')]
    protected ?iterable $oneToManyResultWith = null;

    /**
     * Get the value of oneToManyResultWith.
     *
     * @return Article[]|null
     */
    public function getOneToManyResultWith(): ?iterable
    {
        return $this->oneToManyResultWith;
    }

    /**
     * Set the value of oneToManyResultWith.
     *
     * @param Article[]|null $oneToManyResultWith
     */
    public function setOneToManyResultWith(?iterable $oneToManyResultWith): self
    {
        $this->oneToManyResultWith = $oneToManyResultWith;

        return $this;
    }

    /**
     * @var MemberRoleRelation[]|null
     */
    #[PolymorphicManyToMany(model: 'Role', middle: 'MemberRoleRelation', rightMany: 'manyToManyResultListWith', type: 'type', typeValue: 1, with: true)]
    #[JoinToMiddle(field: 'many_to_many', middleField: 'member_id')]
    #[JoinFromMiddle(middleField: 'role_id', field: 'id')]
    protected ?iterable $manyToManyResultWith = null;

    /**
     * Get the value of manyToManyResultWith.
     *
     * @return MemberRoleRelation[]
     */
    public function getManyToManyResultWith(): ?iterable
    {
        return $this->manyToManyResultWith;
    }

    /**
     * Set the value of manyToManyResultWith.
     *
     * @param MemberRoleRelation[] $manyToManyResultWith
     */
    public function setManyToManyResultWith(?iterable $manyToManyResultWith): self
    {
        $this->manyToManyResultWith = $manyToManyResultWith;

        return $this;
    }

    /**
     * @var Role[]|null
     */
    #[Column(virtual: true)]
    protected ?iterable $manyToManyResultListWith = null;

    /**
     * Get the value of manyToManyResultListWith.
     *
     * @return Role[]|null
     */
    public function getManyToManyResultListWith(): ?iterable
    {
        return $this->manyToManyResultListWith;
    }

    /**
     * Set the value of manyToManyResultListWith.
     *
     * @param Role[]|null $manyToManyResultListWith
     */
    public function setManyToManyResultListWith(?iterable $manyToManyResultListWith): self
    {
        $this->manyToManyResultListWith = $manyToManyResultListWith;

        return $this;
    }

    /**
     * @var Member|Article|null
     */
    #[PolymorphicToOne(model: 'Member', modelField: 'id', type: 'type', typeValue: 1, field: 'to_one', with: true)]
    #[PolymorphicToOne(model: 'Article', modelField: 'id', type: 'type', typeValue: 2, field: 'to_one', with: true)]
    protected ?iterable $toOneResultWith = null;

    /**
     * Get the value of toOneResultWith.
     *
     * @return Member|Article|null
     */
    public function getToOneResultWith()
    {
        return $this->toOneResultWith;
    }

    /**
     * Set the value of toOneResultWith.
     *
     * @param Member|Article|null $toOneResultWith
     */
    public function setToOneResultWith($toOneResultWith): self
    {
        $this->toOneResultWith = $toOneResultWith;

        return $this;
    }

    /**
     * @var Role[]|null
     */
    #[PolymorphicToMany(model: 'Role', modelField: 'id', type: 'type', typeValue: 6, field: 'to_many', middle: 'MemberRoleRelation', middleLeftField: 'role_id', middleRightField: 'member_id', with: true)]
    protected ?iterable $toManyResultWith = null;

    /**
     * Get the value of toManyResultWith.
     *
     * @return Role[]|null
     */
    public function getToManyResultWith(): ?iterable
    {
        return $this->toManyResultWith;
    }

    /**
     * Set the value of toManyResultWith.
     *
     * @param Role[]|null $toManyResultWith
     */
    public function setToManyResultWith(?iterable $toManyResultWith): self
    {
        $this->toManyResultWith = $toManyResultWith;

        return $this;
    }
}
