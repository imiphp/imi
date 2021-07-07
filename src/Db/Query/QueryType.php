<?php

declare(strict_types=1);

namespace Imi\Db\Query;

class QueryType
{
    /**
     * 读.
     */
    public const READ = 1;

    /**
     * 写.
     */
    public const WRITE = 2;

    private function __construct()
    {
    }
}
