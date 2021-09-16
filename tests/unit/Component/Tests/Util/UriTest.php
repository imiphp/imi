<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\Uri;
use function var_dump;

/**
 * @testdox Imi\Util\Uri
 */
class UriTest extends BaseTest
{
    /**
     * @testdox startwith
     */
    public function testUri(): void
    {
        $url = 'https://admin:123456@www.baidu.com/a/b/c.jpg?id=1&name=imi#gg';
        $uri = new Uri($url);
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('admin:123456@www.baidu.com', $uri->getAuthority());
        $this->assertEquals('admin:123456', $uri->getUserInfo());
        $this->assertEquals('www.baidu.com', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertEquals(443, Uri::getServerPort($uri));
        $this->assertEquals('/a/b/c.jpg', $uri->getPath());
        $this->assertEquals('id=1&name=imi', $uri->getQuery());
        $this->assertEquals('gg', $uri->getFragment());
        $this->assertEquals('www.baidu.com', Uri::getDomain($uri));
        $this->assertEquals($url, (string) $uri);
        $this->assertEquals($url, Uri::makeUriString($uri->getHost(), $uri->getPath(), $uri->getQuery(), $uri->getPort(), $uri->getScheme(), $uri->getFragment(), $uri->getUserInfo()));

        $url = 'http://www.imiphp.com:4433/index.html?t=123#ggsmd';
        $uri = $uri->withFragment('ggsmd')->withHost('www.imiphp.com')->withPath('/index.html')->withPort(4433)->withQuery('t=123')->withScheme('http')->withUserInfo('');
        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('www.imiphp.com:4433', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('www.imiphp.com', $uri->getHost());
        $this->assertEquals(4433, $uri->getPort());
        $this->assertEquals(4433, Uri::getServerPort($uri));
        $this->assertEquals('/index.html', $uri->getPath());
        $this->assertEquals('t=123', $uri->getQuery());
        $this->assertEquals('ggsmd', $uri->getFragment());
        $this->assertEquals('www.imiphp.com:4433', Uri::getDomain($uri));
        $this->assertEquals($url, (string) $uri);
        $this->assertEquals($url, Uri::makeUriString($uri->getHost(), $uri->getPath(), $uri->getQuery(), $uri->getPort(), $uri->getScheme(), $uri->getFragment(), $uri->getUserInfo()));


        $url = 'unix:///var/run/redis/redis-server.sock?timeout=60&db=1';
        $uri = new Uri($url);
        $this->assertEquals('unix', $uri->getScheme());
        $this->assertEquals('/var/run/redis/redis-server.sock', $uri->getHost());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('timeout=60&db=1', $uri->getQuery());

        $this->assertEquals($url, (string) $uri);

        $this->assertEquals(
            $url,
            Uri::makeUriString($uri->getHost(), $uri->getPath(), $uri->getQuery(), $uri->getPort(), $uri->getScheme(), $uri->getFragment(), $uri->getUserInfo())
        );
    }
}
