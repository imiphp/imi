<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use PHPUnit\Framework\Assert;
use Yurun\Util\HttpRequest;

/**
 * @testdox Http View
 */
class ViewTest extends BaseTestCase
{
    public function testHtml(): void
    {
        $http = new HttpRequest();
        $time = time();
        $response = $http->get($this->host . 'html?time=' . $time);
        Assert::assertEquals('<p>' . date('Y-m-d H:i:s', $time) . '</p>', $response->body());
    }

    public function testHtml2(): void
    {
        $http = new HttpRequest();
        $time = time();
        $response = $http->get($this->host . 'html2?time=' . $time);
        Assert::assertEquals('<p>tpl2:' . date('Y-m-d H:i:s', $time) . '</p>', $response->body());
    }

    public function testRenderHtml1(): void
    {
        $http = new HttpRequest();
        $time = time();
        $response = $http->get($this->host . 'renderHtml1');
        Assert::assertEquals('hello yurun', $response->body());
    }

    public function testRrenderHtml2(): void
    {
        $http = new HttpRequest();
        $time = time();
        $response = $http->get($this->host . 'renderHtml2');
        Assert::assertEquals('imi niubi', $response->body());
    }

    public function testJson(): void
    {
        $http = new HttpRequest();
        $time = time();
        $response = $http->get($this->host . 'json?time=' . $time);
        Assert::assertEquals([
            'time'  => $time . '',
            'data'  => 'now: ' . date('Y-m-d H:i:s', $time),
        ], $response->json(true));
    }
}
