<?php
namespace Imi\Test\Component\Tests\Db\Mysqli;

use Imi\Test\Component\Tests\Db\QueryCurdBaseTest;

/**
 * @testdox Mysqli MySQL QueryCurd
 */
class QueryCurdTest extends QueryCurdBaseTest
{
    /**
     * 连接池名
     *
     * @var string
     */
    protected $poolName = 'mysqli';

    /**
     * 测试 whereEx 的 SQL
     *
     * @var string
     */
    protected $expectedTestWhereExSql = 'select * from `tb_article` where (`id` = :p1 and (`id` in (:p2) ) )';

}
