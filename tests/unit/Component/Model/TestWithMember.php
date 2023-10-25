<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Relation\AutoSelect;
use Imi\Model\Annotation\Relation\JoinFrom;
use Imi\Model\Annotation\Relation\JoinTo;
use Imi\Model\Annotation\Relation\OneToOne;
use Imi\Model\Annotation\Serializable;
use Imi\Test\Component\Model\Base\TestWithMemberBase;

/**
 * 测试 with member.
 *
 * @property MemberSerializable|null $member
 */
#[Inherit]
class TestWithMember extends TestWithMemberBase
{
    /**
     * member_id.
     */
    #[Inherit]
    #[Serializable(allow: false)]
    protected ?int $memberId = null;

    /**
     * @var MemberSerializable|null
     */
    #[OneToOne(model: 'Imi\\Test\\Component\\Model\\MemberSerializable')]
    #[JoinFrom(field: 'member_id')]
    #[JoinTo(field: 'id')]
    #[AutoSelect(status: false)]
    protected $member = null;

    /**
     * Get the value of member.
     *
     * @return MemberSerializable|null
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set the value of member.
     *
     * @return self
     */
    public function setMember(?MemberSerializable $member)
    {
        $this->member = $member;

        return $this;
    }
}
