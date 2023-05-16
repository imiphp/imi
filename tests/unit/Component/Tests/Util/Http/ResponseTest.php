<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Http;

use Imi\Test\BaseTest;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Http\Response;

class ResponseTest extends BaseTest
{
    public function testWith(): void
    {
        $this->__testX('with');
    }

    public function testSet(): void
    {
        $this->__testX('set');
    }

    private function __testX(string $type): void
    {
        // statusCode
        $response = new Response();
        $changeMethod = $type . 'Status';
        $response = $response->{$changeMethod}(StatusCode::NOT_FOUND);
        $this->assertEquals(StatusCode::NOT_FOUND, $response->getStatusCode());
        $this->assertEquals(StatusCode::getReasonPhrase(StatusCode::NOT_FOUND), $response->getReasonPhrase());

        $response = $response->{$changeMethod}(StatusCode::INTERNAL_SERVER_ERROR, 'test');
        $this->assertEquals(StatusCode::INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals('test', $response->getReasonPhrase());

        // cookie
        $response = new Response();
        $changeMethod = $type . 'Cookie';
        $response = $response->{$changeMethod}('a', '123');
        $this->assertEquals(['a' => [
            'key'      => 'a',
            'value'    => '123',
            'expire'   => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => false,
            'httponly' => false,
        ]], $response->getCookieParams());
        $this->assertEquals([
            'key'      => 'a',
            'value'    => '123',
            'expire'   => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => false,
            'httponly' => false,
        ], $response->getCookie('a'));
        $this->assertEquals('aaa', $response->getCookie('b', 'aaa'));

        // trailer
        $response = new Response();
        $changeMethod = $type . 'Trailer';
        $response = $response->{$changeMethod}('a', '123');
        $this->assertEquals(['a' => '123'], $response->getTrailers());
        $this->assertEquals('123', $response->getTrailer('a'));
        $this->assertNull($response->getTrailer('b'));
    }
}
