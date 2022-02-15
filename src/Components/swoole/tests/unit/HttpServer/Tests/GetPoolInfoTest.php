<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use Imi\Util\Http\Consts\StatusCode;
use Yurun\Util\HttpRequest;

/**
 * @testdox GetPoolInfo
 */
class GetPoolInfoTest extends BaseTest
{
    public function testGetPoolInfo(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'getPoolInfo?poolName=maindb');
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
    }

    public function testGetPoolInfos(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'getPoolInfos');
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
    }
}
