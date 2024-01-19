<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\JsonNotNull;
use Imi\Model\Annotation\Serializables;
use Imi\Model\Test\Model\Base\MemberBase;

/**
 * Member.
 */
#[Inherit]
#[Serializables(mode: 'deny', fields: ['password'])]
class Member extends MemberBase
{
    /**
     * @var null
     */
    #[Column(virtual: true)]
    #[JsonNotNull]
    protected $notInJson = null;

    /**
     * @return null
     */
    public function getNotInJson()
    {
        return $this->notInJson;
    }

    /**
     * @param null $notInJson
     */
    public function setNotInJson($notInJson): self
    {
        $this->notInJson = $notInJson;

        return $this;
    }
}
