<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use PHPUnit\Framework\Assert;
use Yurun\Util\HttpRequest;

/**
 * @testdox Task
 */
class TaskTest extends BaseTest
{
    public function testTask(): void
    {
        $http = new HttpRequest();

        $response = $http->get($this->host . 'task/test');
        $data = $response->json(true);

        Assert::assertIsArray($data);
        Assert::assertIsInt($data['post']);
        Assert::assertIsInt($data['nPost']);
        Assert::assertEquals('2019-06-21 00:00:00', $data['nPostWait']);
        Assert::assertEquals('2019-06-21 00:00:00', $data['postWait']);
        Assert::assertEquals([
            '2018-06-21 00:00:00',
            '2019-06-21 00:00:00',
        ], $data['postCo']);
    }
}
