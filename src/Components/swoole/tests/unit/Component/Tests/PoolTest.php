<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests;

use Imi\Db\Pool\DbResource;
use Imi\RequestContext;
use Imi\Swoole\Test\BaseTestCase;
use Imi\Swoole\Test\Component\Pool\PoolTestClass;

class PoolTest extends BaseTestCase
{
    public function testPoolResource(): void
    {
        $this->go(function (): void {
            $object = RequestContext::getBean(PoolTestClass::class);
            $this->assertInstanceOf(DbResource::class, $object->db);
        });
    }
}
