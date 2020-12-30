<?php

declare(strict_types=1);

namespace Imi\Db\Query;

class Paginate
{
    /**
     * 页码
     *
     * @var int
     */
    public int $page;

    /**
     * 每页记录数.
     *
     * @var int
     */
    public int $count;

    public function __construct(int $page, int $count)
    {
        $this->page = $page;
        $this->count = $count;
    }
}
