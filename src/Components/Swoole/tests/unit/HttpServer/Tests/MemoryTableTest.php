<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use PHPUnit\Framework\Assert;
use Yurun\Util\HttpRequest;

/**
 * @testdox MemoryTable
 */
class MemoryTableTest extends BaseTest
{
    public function testSetAndGet()
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'memoryTable/setAndGet');
        $data = $response->json(true);
        Assert::assertTrue($data['setResult'] ?? null);
        Assert::assertEquals('imi', $data['getField'] ?? null);
        Assert::assertEquals([
            'name'      => 'imi',
            'quantity'  => 0,
        ], $data['getRow'] ?? null);
    }

    public function testDel()
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'memoryTable/del');
        $data = $response->json(true);
        Assert::assertTrue($data['setResult'] ?? null);
        Assert::assertEquals([
            'name'      => 'yurun',
            'quantity'  => 0,
        ], $data['getRow1'] ?? null);
        Assert::assertTrue($data['delResult'] ?? null);
        Assert::assertFalse($data['getRow2']);
    }

    public function testExist()
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'memoryTable/exist');
        $data = $response->json(true);
        Assert::assertFalse($data['existResult1'] ?? null);
        Assert::assertTrue($data['setResult'] ?? null);
        Assert::assertTrue($data['existResult2'] ?? null);
    }

    public function testIncr()
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'memoryTable/incr');
        $data = $response->json(true);
        Assert::assertTrue($data['setResult'] ?? null);
        Assert::assertEquals(1, $data['incrResult'] ?? null);
        Assert::assertEquals(1, $data['getQuantity'] ?? null);
    }

    public function testDecr()
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'memoryTable/decr');
        $data = $response->json(true);
        Assert::assertTrue($data['setResult'] ?? null);
        Assert::assertEquals(-1, $data['decrResult'] ?? null);
        Assert::assertEquals(-1, $data['getQuantity'] ?? null);
    }

    public function testCount()
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'memoryTable/count');
        $data = $response->json(true);
        Assert::assertEquals(4, $data['count'] ?? null);
    }

    public function testLockCallbleSetAndGet()
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'memoryTable/lockCallableSetAndGet');
        $data = $response->json(true);
        Assert::assertTrue($data['setResult'] ?? null);
        Assert::assertEquals('imi', $data['getField'] ?? null);
        Assert::assertEquals([
            'name'      => 'imi',
            'quantity'  => 0,
        ], $data['getRow'] ?? null);
    }

    public function testLockSetAndGet()
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'memoryTable/lockSetAndGet');
        $data = $response->json(true);
        Assert::assertTrue($data['setResult'] ?? null);
        Assert::assertEquals('imi', $data['getField'] ?? null);
        Assert::assertEquals([
            'name'      => 'imi',
            'quantity'  => 0,
        ], $data['getRow'] ?? null);
    }
}
