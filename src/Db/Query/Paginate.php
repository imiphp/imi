<?php

declare(strict_types=1);

namespace Imi\Db\Query;

class Paginate
{
    /**
     * 页码
     */
    public int $page = 0;

    /**
     * 每页记录数.
     */
    public int $count = 0;

    public function __construct(int $page, int $count)
    {
        $this->page = $page;
        $this->count = $count;
    }
}
