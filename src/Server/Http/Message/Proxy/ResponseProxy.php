<?php

namespace Imi\Server\Http\Message\Proxy;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;
use Imi\RequestContextProxy\BaseRequestContextProxy;

/**
 * @Bean(name="HttpResponseProxy", instanceType="singleton")
 * @RequestContextProxy(class="Imi\Util\Http\Contract\IResponse", name="response")
 *
 * @method int                   getStatusCode()
 * @method static                int getStatusCode()
 * @method static                withStatus($code, $reasonPhrase = '')
 * @method static                static withStatus($code, $reasonPhrase = '')
 * @method string                getReasonPhrase()
 * @method static                string getReasonPhrase()
 * @method string                getProtocolVersion()
 * @method static                string getProtocolVersion()
 * @method static                withProtocolVersion($version)
 * @method static                static withProtocolVersion($version)
 * @method string[][]            getHeaders()
 * @method static                string[][] getHeaders()
 * @method bool                  hasHeader($name)
 * @method static                bool hasHeader($name)
 * @method string[]              getHeader($name)
 * @method static                string[] getHeader($name)
 * @method string                getHeaderLine($name)
 * @method static                string getHeaderLine($name)
 * @method static                withHeader($name, $value)
 * @method static                static withHeader($name, $value)
 * @method static                withAddedHeader($name, $value)
 * @method static                static withAddedHeader($name, $value)
 * @method static                withoutHeader($name)
 * @method static                static withoutHeader($name)
 * @method StreamInterface       getBody()
 * @method static                StreamInterface getBody()
 * @method static                withBody(\Psr\Http\Message\StreamInterface $body)
 * @method static                static withBody(\Psr\Http\Message\StreamInterface $body)
 * @method static                withCookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
 * @method static                static withCookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
 * @method array                 getTrailers()
 * @method static                array getTrailers()
 * @method bool                  hasTrailer($name)
 * @method static                bool hasTrailer($name)
 * @method string|null           getTrailer($name)
 * @method static                string|null getTrailer($name)
 * @method static                withTrailer($name, $value)
 * @method static                static withTrailer($name, $value)
 * @method static                write(string $content)
 * @method static                static write(string $content)
 * @method static                clear()
 * @method static                static clear()
 * @method static                redirect($url, $status = 302)
 * @method static                static redirect($url, $status = 302)
 * @method static                sendHeaders()
 * @method static                static sendHeaders()
 * @method static                send()
 * @method static                static send()
 * @method static                sendFile(string $filename, int $offset = 0, int $length = 0)
 * @method static                static sendFile(string $filename, int $offset = 0, int $length = 0)
 * @method \Swoole\Http\Response getSwooleResponse()
 * @method static                \Swoole\Http\Response getSwooleResponse()
 * @method \Imi\Server\Base      getServerInstance()
 * @method static                \Imi\Server\Base getServerInstance()
 * @method bool                  isEnded()
 * @method static                bool isEnded()
 */
class ResponseProxy extends BaseRequestContextProxy implements \Imi\Util\Http\Contract\IResponse
{
    /**
     * {@inheritDoc}
     */
    public function getStatusCode()
    {
        return $thisself::__getProxyInstance()->getStatusCode(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        return $thisself::__getProxyInstance()->withStatus($code, $reasonPhrase);
    }

    /**
     * {@inheritDoc}
     */
    public function getReasonPhrase()
    {
        return $thisself::__getProxyInstance()->getReasonPhrase(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getProtocolVersion()
    {
        return $thisself::__getProxyInstance()->getProtocolVersion(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withProtocolVersion($version)
    {
        return $thisself::__getProxyInstance()->withProtocolVersion($version);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        return $thisself::__getProxyInstance()->getHeaders(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function hasHeader($name)
    {
        return $thisself::__getProxyInstance()->hasHeader($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeader($name)
    {
        return $thisself::__getProxyInstance()->getHeader($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaderLine($name)
    {
        return $thisself::__getProxyInstance()->getHeaderLine($name);
    }

    /**
     * {@inheritDoc}
     */
    public function withHeader($name, $value)
    {
        return $thisself::__getProxyInstance()->withHeader($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function withAddedHeader($name, $value)
    {
        return $thisself::__getProxyInstance()->withAddedHeader($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function withoutHeader($name)
    {
        return $thisself::__getProxyInstance()->withoutHeader($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getBody()
    {
        return $thisself::__getProxyInstance()->getBody(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withBody(\Psr\Http\Message\StreamInterface $body)
    {
        return $thisself::__getProxyInstance()->withBody($body);
    }

    /**
     * {@inheritDoc}
     */
    public static function getInstance(\Imi\Server\Base $server, \Swoole\Http\Response $response)
    {
        throw new \RuntimeException('Unsupport method');
    }

    /**
     * {@inheritDoc}
     */
    public function withCookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
    {
        return $thisself::__getProxyInstance()->withCookie($key, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * {@inheritDoc}
     */
    public function getTrailers()
    {
        return $thisself::__getProxyInstance()->getTrailers(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function hasTrailer($name)
    {
        return $thisself::__getProxyInstance()->hasTrailer($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getTrailer($name)
    {
        return $thisself::__getProxyInstance()->getTrailer($name);
    }

    /**
     * {@inheritDoc}
     */
    public function withTrailer($name, $value)
    {
        return $thisself::__getProxyInstance()->withTrailer($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $content)
    {
        return $thisself::__getProxyInstance()->write($content);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        return $thisself::__getProxyInstance()->clear(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function redirect($url, $status = 302)
    {
        return $thisself::__getProxyInstance()->redirect($url, $status);
    }

    /**
     * {@inheritDoc}
     */
    public function sendHeaders()
    {
        return $thisself::__getProxyInstance()->sendHeaders(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function send()
    {
        return $thisself::__getProxyInstance()->send(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function sendFile(string $filename, int $offset = 0, int $length = 0)
    {
        return $thisself::__getProxyInstance()->sendFile($filename, $offset, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function getSwooleResponse(): \Swoole\Http\Response
    {
        return $thisself::__getProxyInstance()->getSwooleResponse(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getServerInstance(): \Imi\Server\Base
    {
        return $thisself::__getProxyInstance()->getServerInstance(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function isEnded()
    {
        return $thisself::__getProxyInstance()->isEnded(...\func_get_args());
    }
}
