<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;

/**
 * Member.
 *
 * @property int|null $id2
 * @Inherit
 */
class MemberReferenceProperty extends Member
{
    /**
     * @Column(virtual=true, reference="id")
     */
    protected ?int $id2;

    public function getId2(): ?int
    {
        return $this->id2;
    }

    /**
     * @return self
     */
    public function setId2(?int $id2)
    {
        $this->id2 = $id2;

        return $this;
    }
}
