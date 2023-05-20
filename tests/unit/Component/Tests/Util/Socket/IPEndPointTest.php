<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Socket;

use Imi\Test\BaseTest;
use Imi\Util\Socket\IPEndPoint;

class IPEndPointTest extends BaseTest
{
    public function test(): void
    {
        $point = new IPEndPoint('127.0.0.1', 80);
        $this->assertEquals('127.0.0.1', $point->getAddress());
        $this->assertEquals(80, $point->getPort());
        $this->assertEquals('127.0.0.1:80', (string) $point);
    }
}
