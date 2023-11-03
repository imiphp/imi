<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

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
use Imi\Test\Component\Model\Base\PolymorphicBase;

/**
 * tb_polymorphic.
 */
#[Inherit]
class Polymorphic extends PolymorphicBase
{
    /**
     * @var Article|null
     */
    #[PolymorphicOneToOne(model: 'Article', type: 'title', typeValue: 'PolymorphicOneToOne')]
    #[JoinFrom(field: 'one_to_one')]
    #[JoinTo(field: 'member_id')]
    protected $oneToOneResult;

    /**
     * Get the value of oneToOneResult.
     *
     * @return Article|null
     */
    public function getOneToOneResult()
    {
        return $this->oneToOneResult;
    }

    /**
     * Set the value of oneToOneResult.
     *
     * @return self
     */
    public function setOneToOneResult(?Article $oneToOneResult)
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
    protected $oneToManyResult = null;

    /**
     * Get the value of oneToManyResult.
     *
     * @return Article[]|null
     */
    public function getOneToManyResult()
    {
        return $this->oneToManyResult;
    }

    /**
     * Set the value of oneToManyResult.
     *
     * @param Article[]|null $oneToManyResult
     *
     * @return self
     */
    public function setOneToManyResult(?array $oneToManyResult)
    {
        $this->oneToManyResult = $oneToManyResult;

        return $this;
    }

    /**
     * @var MemberRoleRelation|null
     */
    #[PolymorphicManyToMany(model: 'Role', middle: 'MemberRoleRelation', rightMany: 'manyToManyResultList', type: 'type', typeValue: 1)]
    #[JoinToMiddle(field: 'many_to_many', middleField: 'member_id')]
    #[JoinFromMiddle(middleField: 'role_id', field: 'id')]
    protected $manyToManyResult = null;

    /**
     * Get the value of manyToManyResult.
     *
     * @return MemberRoleRelation|null
     */
    public function getManyToManyResult()
    {
        return $this->manyToManyResult;
    }

    /**
     * Set the value of manyToManyResult.
     *
     * @return self
     */
    public function setManyToManyResult(?MemberRoleRelation $manyToManyResult)
    {
        $this->manyToManyResult = $manyToManyResult;

        return $this;
    }

    /**
     * @var Role[]|null
     */
    #[Column(virtual: true)]
    protected $manyToManyResultList;

    /**
     * Get the value of manyToManyResultList.
     *
     * @return Role[]|null
     */
    public function getManyToManyResultList()
    {
        return $this->manyToManyResultList;
    }

    /**
     * Set the value of manyToManyResultList.
     *
     * @param Role[]|null $manyToManyResultList
     *
     * @return self
     */
    public function setManyToManyResultList(?array $manyToManyResultList)
    {
        $this->manyToManyResultList = $manyToManyResultList;

        return $this;
    }

    /**
     * @var Member|Article|null
     */
    #[PolymorphicToOne(model: 'Member', modelField: 'id', type: 'type', typeValue: 1, field: 'to_one')]
    #[PolymorphicToOne(model: 'Article', modelField: 'id', type: 'type', typeValue: 2, field: 'to_one')]
    protected $toOneResult;

    /**
     * Get the value of toOneResult.
     *
     * @return Member|Article|null
     */
    public function getToOneResult()
    {
        return $this->toOneResult;
    }

    /**
     * Set the value of toOneResult.
     *
     * @param Member|Article|null $toOneResult
     *
     * @return self
     */
    public function setToOneResult($toOneResult)
    {
        $this->toOneResult = $toOneResult;

        return $this;
    }

    /**
     * @var Role[]|null
     */
    #[PolymorphicToMany(model: 'Role', modelField: 'id', type: 'type', typeValue: 6, field: 'to_many', middle: 'MemberRoleRelation', middleLeftField: 'role_id', middleRightField: 'member_id')]
    protected $toManyResult;

    /**
     * Get the value of toManyResult.
     *
     * @return Role[]|null
     */
    public function getToManyResult()
    {
        return $this->toManyResult;
    }

    /**
     * Set the value of toManyResult.
     *
     * @param Role[]|null $toManyResult
     *
     * @return self
     */
    public function setToManyResult(?array $toManyResult)
    {
        $this->toManyResult = $toManyResult;

        return $this;
    }

    /**
     * @var Article|null
     */
    #[PolymorphicOneToOne(model: 'Article', type: 'title', typeValue: 'PolymorphicOneToOne', with: true)]
    #[JoinFrom(field: 'one_to_one')]
    #[JoinTo(field: 'member_id')]
    protected $oneToOneResultWith;

    /**
     * Get the value of oneToOneResultWith.
     *
     * @return Article|null
     */
    public function getOneToOneResultWith()
    {
        return $this->oneToOneResultWith;
    }

    /**
     * Set the value of oneToOneResultWith.
     *
     * @return self
     */
    public function setOneToOneResultWith(?Article $oneToOneResultWith)
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
    protected $oneToManyResultWith = null;

    /**
     * Get the value of oneToManyResultWith.
     *
     * @return Article[]|null
     */
    public function getOneToManyResultWith()
    {
        return $this->oneToManyResultWith;
    }

    /**
     * Set the value of oneToManyResultWith.
     *
     * @param Article[]|null $oneToManyResultWith
     *
     * @return self
     */
    public function setOneToManyResultWith(?array $oneToManyResultWith)
    {
        $this->oneToManyResultWith = $oneToManyResultWith;

        return $this;
    }

    /**
     * @var MemberRoleRelation|null
     */
    #[PolymorphicManyToMany(model: 'Role', middle: 'MemberRoleRelation', rightMany: 'manyToManyResultListWith', type: 'type', typeValue: 1, with: true)]
    #[JoinToMiddle(field: 'many_to_many', middleField: 'member_id')]
    #[JoinFromMiddle(middleField: 'role_id', field: 'id')]
    protected $manyToManyResultWith = null;

    /**
     * Get the value of manyToManyResultWith.
     *
     * @return MemberRoleRelation|null
     */
    public function getManyToManyResultWith()
    {
        return $this->manyToManyResultWith;
    }

    /**
     * Set the value of manyToManyResultWith.
     *
     * @return self
     */
    public function setManyToManyResultWith(?MemberRoleRelation $manyToManyResultWith)
    {
        $this->manyToManyResultWith = $manyToManyResultWith;

        return $this;
    }

    /**
     * @var Role[]|null
     */
    #[Column(virtual: true)]
    protected $manyToManyResultListWith;

    /**
     * Get the value of manyToManyResultListWith.
     *
     * @return Role[]|null
     */
    public function getManyToManyResultListWith()
    {
        return $this->manyToManyResultListWith;
    }

    /**
     * Set the value of manyToManyResultListWith.
     *
     * @param Role[]|null $manyToManyResultListWith
     *
     * @return self
     */
    public function setManyToManyResultListWith(?array $manyToManyResultListWith)
    {
        $this->manyToManyResultListWith = $manyToManyResultListWith;

        return $this;
    }

    /**
     * @var Member|Article|null
     */
    #[PolymorphicToOne(model: 'Member', modelField: 'id', type: 'type', typeValue: 1, field: 'to_one', with: true)]
    #[PolymorphicToOne(model: 'Article', modelField: 'id', type: 'type', typeValue: 2, field: 'to_one', with: true)]
    protected $toOneResultWith;

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
     *
     * @return self
     */
    public function setToOneResultWith($toOneResultWith)
    {
        $this->toOneResultWith = $toOneResultWith;

        return $this;
    }

    /**
     * @var Role[]|null
     */
    #[PolymorphicToMany(model: 'Role', modelField: 'id', type: 'type', typeValue: 6, field: 'to_many', middle: 'MemberRoleRelation', middleLeftField: 'role_id', middleRightField: 'member_id', with: true)]
    protected $toManyResultWith;

    /**
     * Get the value of toManyResultWith.
     *
     * @return Role[]|null
     */
    public function getToManyResultWith()
    {
        return $this->toManyResultWith;
    }

    /**
     * Set the value of toManyResultWith.
     *
     * @param Role[]|null $toManyResultWith
     *
     * @return self
     */
    public function setToManyResultWith(?array $toManyResultWith)
    {
        $this->toManyResultWith = $toManyResultWith;

        return $this;
    }
}
