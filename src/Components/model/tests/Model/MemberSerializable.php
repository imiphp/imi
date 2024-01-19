<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Serializable;
use Imi\Model\Test\Model\Base\MemberBase;

/**
 * tb_member.
 */
#[Inherit]
class MemberSerializable extends MemberBase
{
    /**
     * 密码.
     * password.
     */
    #[Inherit]
    #[Serializable(allow: false)]
    protected ?string $password = null;
}
