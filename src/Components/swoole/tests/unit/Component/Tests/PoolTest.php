<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests;

use Imi\Db\Pool\DbResource;
use Imi\RequestContext;
use Imi\Swoole\Test\BaseTest;
use Imi\Swoole\Test\Component\Pool\PoolTestClass;

class PoolTest extends BaseTest
{
    public function testPoolResource(): void
    {
        $this->go(function () {
            $object = RequestContext::getBean(PoolTestClass::class);
            $this->assertInstanceOf(DbResource::class, $object->db);
        });
    }
}
