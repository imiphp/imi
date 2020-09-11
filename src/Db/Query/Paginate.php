<?php

namespace Imi\Db\Query;

class Paginate
{
    /**
     * 页码
     *
     * @var int
     */
    public $page;

    /**
     * 每页记录数.
     *
     * @var int
     */
    public $count;

    public function __construct($page, $count)
    {
        $this->page = $page;
        $this->count = $count;
    }
}
