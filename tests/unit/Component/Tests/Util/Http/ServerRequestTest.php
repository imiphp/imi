<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Http;

use Imi\Config;
use Imi\RequestContext;
use Imi\Server\Http\Message\UploadedFile;
use Imi\Server\ServerManager;
use Imi\Test\BaseTest;
use Imi\Test\Component\Server\TestServer;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\RequestHeader;
use Imi\Util\Http\Consts\RequestMethod;
use Imi\Util\Http\ServerRequest;
use Imi\Util\Stream\MemoryStream;
use Imi\Util\Uri;

class ServerRequestTest extends BaseTest
{
    public static function setUpBeforeClass(): void
    {
        $server = ServerManager::createServer(__CLASS__, ['type' => TestServer::class]);
        RequestContext::set('server', $server);
        Config::addConfig('@server', []);
        Config::set('@server.' . __CLASS__, []);
    }

    public static function tearDownAfterClass(): void
    {
        RequestContext::unset('server');
    }

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
        $url = 'http://127.0.0.1:8080/test?id=1';
        foreach ([
            'ProtocolVersion' => '1.1',
            'Body'            => new MemoryStream('test'),
            'Method'          => RequestMethod::POST,
            'Uri'             => new Uri($url),
            'QueryParams'     => ['id' => '1'],
            'UploadedFiles'   => [
                new UploadedFile('1.jpg', MediaType::IMAGE_JPEG, '/tmp/1.jpg', 1024, 0),
            ],
            'ParsedBody' => ['name' => 'imi'],
        ] as $name => $value)
        {
            $request = new ServerRequest();
            $changeMethod = $type . $name;
            $getMethod = 'get' . $name;
            $request = $request->{$changeMethod}($value);
            $this->assertEquals($value, $request->{$getMethod}(), 'Test ' . $type . $name);
        }

        $request = new ServerRequest();
        $this->assertNull($request->getUri());

        $this->__testHeader($type);
        $this->__testParsedBody($type);

        // requestTarget
        $request = new ServerRequest();
        $request->setUri(new Uri($url));
        $this->assertEquals('/test?id=1', $request->getRequestTarget());
        $changeMethod = $type . 'RequestTarget';
        $request = $request->{$changeMethod}('/test');
        $this->assertEquals('/test', $request->getRequestTarget(), 'Test ' . $type . 'RequestTarget');

        // serverParams
        $request = new ServerRequest();
        $this->assertEquals([], $request->getServerParams());
        $this->assertEquals(1, $request->getServerParam('test', 1));

        // cookie
        $request = new ServerRequest();
        $changeMethod = $type . 'CookieParams';
        $request = $request->{$changeMethod}(['a' => '123']);
        $this->assertEquals(['a' => '123'], $request->getCookieParams());
        $this->assertEquals('123', $request->getCookie('a'));
        $this->assertEquals('aaa', $request->getCookie('b', 'aaa'));

        // attribute
        $request = new ServerRequest();
        $changeMethod = $type . 'Attribute';
        $request = $request->{$changeMethod}('a', '123');
        $this->assertEquals('123', $request->getAttribute('a'));
        $this->assertEquals('aaa', $request->getAttribute('b', 'aaa'));
        $this->assertEquals(['a' => '123'], $request->getAttributes());
        $changeMethod = 'with' === $type ? 'withoutAttribute' : 'removeAttribute';
        $request = $request->{$changeMethod}('a');
        $this->assertEquals([], $request->getAttributes());
    }

    private function __testHeader(string $type)
    {
        // withHeader/setHeader
        $request = new ServerRequest();
        $changeMethod = $type . 'Header';
        // 改变 2 次
        $this->assertFalse($request->hasHeader('name'));
        $request = $request->{$changeMethod}('name', 'php');
        $request = $request->{$changeMethod}('name', 'imi');
        $this->assertTrue($request->hasHeader('name'));
        $this->assertEquals('imi', $request->getHeaderLine('name'));
        $this->assertEquals(['imi'], $request->getHeader('name'));
        $this->assertEquals(['name' => ['imi']], $request->getHeaders());

        // withAddedHeader/addHeader
        $request = new ServerRequest();
        $changeMethod = 'with' === $type ? 'withAddedHeader' : 'addHeader';
        // 增加 2 次
        $this->assertFalse($request->hasHeader('imi'));
        $request = $request->{$changeMethod}('imi', 'niubi');
        $request = $request->{$changeMethod}('imi', 'test');
        $this->assertTrue($request->hasHeader('imi'));
        $this->assertEquals('niubi,test', $request->getHeaderLine('imi'));
        $this->assertEquals(['niubi', 'test'], $request->getHeader('imi'));
        $this->assertEquals(['imi' => ['niubi', 'test']], $request->getHeaders());

        // withoutHeader/removeHeader
        $changeMethod = 'with' === $type ? 'withoutHeader' : 'removeHeader';
        $this->assertTrue($request->hasHeader('imi'));
        $request = $request->{$changeMethod}('imi');
        $this->assertFalse($request->hasHeader('imi'));
    }

    private function __testParsedBody(string $type): void
    {
        $request = new ServerRequest();
        $changeMethod = $type . 'ParsedBody';
        $request = $request->{$changeMethod}(['name' => 'imi']);
        $this->assertEquals(['name' => 'imi'], $request->getParsedBody());

        // json array
        Config::set('@server.' . __CLASS__ . '.jsonBodyIsObject', false);
        $request = new ServerRequest();
        $request->setBody(new MemoryStream(json_encode(['name' => 'imi'])))
                ->setHeader(RequestHeader::CONTENT_TYPE, MediaType::APPLICATION_JSON);
        $this->assertEquals(['name' => 'imi'], $request->getParsedBody());
        $this->assertEquals($request->getParsedBody(), $request->post());

        // json object
        Config::set('@server.' . __CLASS__ . '.jsonBodyIsObject', true);
        $request = new ServerRequest();
        $request->setBody(new MemoryStream(json_encode(['name' => 'imi'])))
                ->setHeader(RequestHeader::CONTENT_TYPE, MediaType::APPLICATION_JSON);
        $parsedBody = $request->getParsedBody();
        $this->assertInstanceOf(\stdClass::class, $parsedBody);
        $this->assertEquals('imi', $parsedBody->name ?? null);
        $this->assertEquals($request->getParsedBody(), $request->post());

        // xml
        $xml = <<<'XML'
        <?xml version="1.0"?>
        <xml><name>imi</name></xml>

        XML;
        $request = new ServerRequest();
        $request->setBody(new MemoryStream($xml))
                ->setHeader(RequestHeader::CONTENT_TYPE, MediaType::APPLICATION_XML);
        /** @var \DOMDocument $parsedBody */
        $parsedBody = $request->getParsedBody();
        $this->assertInstanceOf(\DOMDocument::class, $parsedBody);
        $this->assertEquals($xml, $parsedBody->saveXML());
    }
}
