<?php

namespace Imi\Db\Query;

abstract class QueryType
{
    /**
     * 读.
     */
    const READ = 1;

    /**
     * 写.
     */
    const WRITE = 2;
}
