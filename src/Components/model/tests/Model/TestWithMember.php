<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Relation\AutoSelect;
use Imi\Model\Annotation\Relation\JoinFrom;
use Imi\Model\Annotation\Relation\JoinTo;
use Imi\Model\Annotation\Relation\OneToOne;
use Imi\Model\Annotation\Serializable;
use Imi\Model\Test\Model\Base\TestWithMemberBase;

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

    #[OneToOne(model: MemberSerializable::class)]
    #[JoinFrom(field: 'member_id')]
    #[JoinTo(field: 'id')]
    #[AutoSelect(status: false)]
    protected ?MemberSerializable $member = null;

    /**
     * Get the value of member.
     */
    public function getMember(): ?MemberSerializable
    {
        return $this->member;
    }

    /**
     * Set the value of member.
     */
    public function setMember(?MemberSerializable $member): self
    {
        $this->member = $member;

        return $this;
    }
}
