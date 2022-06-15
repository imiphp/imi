<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Serializable;
use Imi\Test\Component\Model\Base\MemberBase;

/**
 * tb_member.
 *
 * @Inherit
 */
class MemberSerializable extends MemberBase
{
    /**
     * 密码.
     * password.
     *
     * @Inherit
     * @Serializable(false)
     */
    protected ?string $password = null;
}
