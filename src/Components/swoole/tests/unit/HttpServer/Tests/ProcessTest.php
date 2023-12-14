<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

/**
 * @testdox Process
 */
class ProcessTest extends BaseTestCase
{
    /**
     * getProcessWithManager.
     */
    public function testGetProcessWithManager(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'process');
        $data = $response->json(true);
        $this->assertTrue($data['result'] ?? null);
    }
}
