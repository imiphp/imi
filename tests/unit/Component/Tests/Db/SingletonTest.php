<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Db;

use Imi\Db\Db;
use Imi\Test\BaseTest;

class SingletonTest extends BaseTest
{
    public const CONNECTION_NAME = 'tradition';

    public function testGetInstance(): void
    {
        $a = Db::getInstance(self::CONNECTION_NAME);
        $b = Db::getInstance(self::CONNECTION_NAME);
        $this->assertEquals(spl_object_hash($a), spl_object_hash($b));
        $this->assertEquals([1], $a->query('select 1')->fetch(\PDO::FETCH_NUM));
    }

    public function testGetNewInstance(): void
    {
        $a = Db::getInstance(self::CONNECTION_NAME);
        $b = Db::getNewInstance(self::CONNECTION_NAME);
        $this->assertNotEquals(spl_object_hash($a), spl_object_hash($b));
        $this->assertEquals([2], $b->query('select 2')->fetch(\PDO::FETCH_NUM));
    }
}
