<?php

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\JsonNotNull;
use Imi\Model\Annotation\Serializables;
use Imi\Test\Component\Model\Base\MemberBase;

/**
 * Member.
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
     * Get the value of notInJson.
     *
     * @return null
     */
    public function getNotInJson()
    {
        return $this->notInJson;
    }
}
