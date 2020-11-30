<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Db\Pdo;

use Imi\Test\Component\Tests\Db\QueryCurdBaseTest;

/**
 * @testdox PdoQueryCurd
 */
class QueryCurdTest extends QueryCurdBaseTest
{
    /**
     * 连接池名.
     *
     * @var string
     */
    protected $poolName = 'maindb';

    /**
     * 测试 whereEx 的 SQL.
     *
     * @var string
     */
    protected $expectedTestWhereExSql = 'select * from `tb_article` where (`id` = :p1 and (`id` in (:p2) ) )';
}
