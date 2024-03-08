<?php

declare(strict_types=1);

namespace Imi\Redis\Test\Tests\Model\PhpRedisCluster;

use Imi\Redis\Test\Tests\Model\AbstractRedisModelHashObject;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Redis/PhpRedisCluster/Model/HashObjectModel')]
class PhpRedisModelHashObjectTest extends AbstractRedisModelHashObject
{
    protected string $poolName = 'test_phpredis_cluster';

    public static function setUpBeforeClass(): void
    {
        if (\PHP_OS_FAMILY !== 'Linux')
        {
            self::markTestSkipped('not support redis cluster');
        }
    }
}