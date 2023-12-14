<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Test\Model\Base\TestListBase;

/**
 * tb_test_list.
 *
 * @property array|null $list
 */
#[Inherit]
class TestList extends TestListBase
{
    /**
     * list.
     *
     * @var array|null
     */
    #[Column(name: 'list', type: 'list', listSeparator: ',', length: 255, nullable: false, default: '')]
    protected $list; // @phpstan-ignore-line

    /**
     * 获取 list.
     *
     * @phpstan-ignore-next-line
     *
     * @return array|null
     */
    public function getList(): mixed
    {
        return $this->list;
    }

    /**
     * 赋值 list.
     *
     * @phpstan-ignore-next-line
     *
     * @param array|null $list list
     *
     * @return static
     */
    public function setList(mixed $list): self
    {
        $this->list = $list;

        return $this;
    }
}
