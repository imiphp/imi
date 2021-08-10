<?php

namespace Imi\Server\Http\Message\Proxy;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;
use Imi\RequestContextProxy\BaseRequestContextProxy;

/**
 * @Bean(name="HttpRequestProxy", instanceType="singleton")
 * @RequestContextProxy(class="Imi\Util\Http\Contract\IServerRequest", name="request")
 *
 * @method mixed                get($name = NULL, $default = NULL)
 * @method static               mixed get($name = NULL, $default = NULL)
 * @method mixed                post($name = NULL, $default = NULL)
 * @method static               mixed post($name = NULL, $default = NULL)
 * @method bool                 hasGet($name)
 * @method static               bool hasGet($name)
 * @method bool                 hasPost($name)
 * @method static               bool hasPost($name)
 * @method mixed                request($name = NULL, $default = NULL)
 * @method static               mixed request($name = NULL, $default = NULL)
 * @method bool                 hasRequest($name)
 * @method static               bool hasRequest($name)
 * @method \Swoole\Http\Request getSwooleRequest()
 * @method static               \Swoole\Http\Request getSwooleRequest()
 * @method \Imi\Server\Base     getServerInstance()
 * @method static               \Imi\Server\Base getServerInstance()
 * @method array                getServerParams()
 * @method static               array getServerParams()
 * @method array                getCookieParams()
 * @method static               array getCookieParams()
 * @method static               withCookieParams(array $cookies)
 * @method static               static withCookieParams(array $cookies)
 * @method array                getQueryParams()
 * @method static               array getQueryParams()
 * @method static               withQueryParams(array $query)
 * @method static               static withQueryParams(array $query)
 * @method array                getUploadedFiles()
 * @method static               array getUploadedFiles()
 * @method static               withUploadedFiles(array $uploadedFiles)
 * @method static               static withUploadedFiles(array $uploadedFiles)
 * @method array|object|null    getParsedBody()
 * @method static               null|array|object getParsedBody()
 * @method static               withParsedBody($data)
 * @method static               static withParsedBody($data)
 * @method array                getAttributes()
 * @method static               array getAttributes()
 * @method mixed                getAttribute($name, $default = NULL)
 * @method static               mixed getAttribute($name, $default = NULL)
 * @method static               withAttribute($name, $value)
 * @method static               static withAttribute($name, $value)
 * @method static               withoutAttribute($name)
 * @method static               static withoutAttribute($name)
 * @method string               getRequestTarget()
 * @method static               string getRequestTarget()
 * @method static               withRequestTarget($requestTarget)
 * @method static               static withRequestTarget($requestTarget)
 * @method string               getMethod()
 * @method static               string getMethod()
 * @method static               withMethod($method)
 * @method static               static withMethod($method)
 * @method UriInterface         getUri()
 * @method static               UriInterface getUri()
 * @method static               withUri(\Psr\Http\Message\UriInterface $uri, $preserveHost = false)
 * @method static               static withUri(\Psr\Http\Message\UriInterface $uri, $preserveHost = false)
 * @method string               getProtocolVersion()
 * @method static               string getProtocolVersion()
 * @method static               withProtocolVersion($version)
 * @method static               static withProtocolVersion($version)
 * @method string[][]           getHeaders()
 * @method static               string[][] getHeaders()
 * @method bool                 hasHeader($name)
 * @method static               bool hasHeader($name)
 * @method string[]             getHeader($name)
 * @method static               string[] getHeader($name)
 * @method string               getHeaderLine($name)
 * @method static               string getHeaderLine($name)
 * @method static               withHeader($name, $value)
 * @method static               static withHeader($name, $value)
 * @method static               withAddedHeader($name, $value)
 * @method static               static withAddedHeader($name, $value)
 * @method static               withoutHeader($name)
 * @method static               static withoutHeader($name)
 * @method StreamInterface      getBody()
 * @method static               StreamInterface getBody()
 * @method static               withBody(\Psr\Http\Message\StreamInterface $body)
 * @method static               static withBody(\Psr\Http\Message\StreamInterface $body)
 */
class RequestProxy extends BaseRequestContextProxy implements \Imi\Util\Http\Contract\IServerRequest
{
    /**
     * {@inheritDoc}
     */
    public function get($name = null, $default = null)
    {
        return self::__getProxyInstance()->get($name, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function post($name = null, $default = null)
    {
        return self::__getProxyInstance()->post($name, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function hasGet($name)
    {
        return self::__getProxyInstance()->hasGet($name);
    }

    /**
     * {@inheritDoc}
     */
    public function hasPost($name)
    {
        return self::__getProxyInstance()->hasPost($name);
    }

    /**
     * {@inheritDoc}
     */
    public function request($name = null, $default = null)
    {
        return self::__getProxyInstance()->request($name, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function hasRequest($name)
    {
        return self::__getProxyInstance()->hasRequest($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getSwooleRequest(): \Swoole\Http\Request
    {
        return self::__getProxyInstance()->getSwooleRequest(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getServerInstance(): \Imi\Server\Base
    {
        return self::__getProxyInstance()->getServerInstance(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getServerParams()
    {
        return self::__getProxyInstance()->getServerParams(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getCookieParams()
    {
        return self::__getProxyInstance()->getCookieParams(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withCookieParams(array $cookies)
    {
        return self::__getProxyInstance()->withCookieParams($cookies);
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryParams()
    {
        return self::__getProxyInstance()->getQueryParams(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withQueryParams(array $query)
    {
        return self::__getProxyInstance()->withQueryParams($query);
    }

    /**
     * {@inheritDoc}
     */
    public function getUploadedFiles()
    {
        return self::__getProxyInstance()->getUploadedFiles(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        return self::__getProxyInstance()->withUploadedFiles($uploadedFiles);
    }

    /**
     * {@inheritDoc}
     */
    public function getParsedBody()
    {
        return self::__getProxyInstance()->getParsedBody(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withParsedBody($data)
    {
        return self::__getProxyInstance()->withParsedBody($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes()
    {
        return self::__getProxyInstance()->getAttributes(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute($name, $default = null)
    {
        return self::__getProxyInstance()->getAttribute($name, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function withAttribute($name, $value)
    {
        return self::__getProxyInstance()->withAttribute($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function withoutAttribute($name)
    {
        return self::__getProxyInstance()->withoutAttribute($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestTarget()
    {
        return self::__getProxyInstance()->getRequestTarget(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withRequestTarget($requestTarget)
    {
        return self::__getProxyInstance()->withRequestTarget($requestTarget);
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod()
    {
        return self::__getProxyInstance()->getMethod(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withMethod($method)
    {
        return self::__getProxyInstance()->withMethod($method);
    }

    /**
     * {@inheritDoc}
     */
    public function getUri()
    {
        return self::__getProxyInstance()->getUri(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withUri(\Psr\Http\Message\UriInterface $uri, $preserveHost = false)
    {
        return self::__getProxyInstance()->withUri($uri, $preserveHost);
    }

    /**
     * {@inheritDoc}
     */
    public function getProtocolVersion()
    {
        return self::__getProxyInstance()->getProtocolVersion(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withProtocolVersion($version)
    {
        return self::__getProxyInstance()->withProtocolVersion($version);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        return self::__getProxyInstance()->getHeaders(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function hasHeader($name)
    {
        return self::__getProxyInstance()->hasHeader($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeader($name)
    {
        return self::__getProxyInstance()->getHeader($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaderLine($name)
    {
        return self::__getProxyInstance()->getHeaderLine($name);
    }

    /**
     * {@inheritDoc}
     */
    public function withHeader($name, $value)
    {
        return self::__getProxyInstance()->withHeader($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function withAddedHeader($name, $value)
    {
        return self::__getProxyInstance()->withAddedHeader($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function withoutHeader($name)
    {
        return self::__getProxyInstance()->withoutHeader($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getBody()
    {
        return self::__getProxyInstance()->getBody(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withBody(\Psr\Http\Message\StreamInterface $body)
    {
        return self::__getProxyInstance()->withBody($body);
    }
}
