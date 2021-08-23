<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\JsonNotNull;
use Imi\Model\Annotation\Serializables;
use Imi\Pgsql\Test\Model\Base\MemberBase;

/**
 * tb_member.
 *
 * @Inherit
 * @Serializables(mode="deny", fields={"password"})
 */
class Member extends MemberBase
{
    /**
     * @Column(virtual=true)
     * @JsonNotNull
     *
     * @var null
     */
    protected $notInJson = null;

    /**
     * @return null
     */
    public function getNotInJson()
    {
        return $this->notInJson;
    }
}
