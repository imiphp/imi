<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Test\Model\Base\TreeBase;

/**
 * @property int[] $list
 */
#[Entity]
class ReferenceGetterTestModel extends TreeBase
{
    /**
     * @var int[]
     */
    #[Column(virtual: true)]
    protected array $list = [];

    /**
     * @return int[]
     */
    public function &getList(): array
    {
        return $this->list;
    }

    /**
     * @param int[] $list
     */
    public function setList(array $list): self
    {
        $this->list = $list;

        return $this;
    }
}
