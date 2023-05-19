<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests;

use Imi\App;
use Imi\Swoole\Db\Pool\CoroutineDbPool;
use Imi\Swoole\Test\BaseTest;
use Imi\Swoole\Test\Component\Pool\PoolTestClass;

class PoolTest extends BaseTest
{
    public function testPoolResource(): void
    {
        $object = App::getBean(PoolTestClass::class);
        $this->assertInstanceOf(CoroutineDbPool::class, $object->maindbPool);
    }
}
