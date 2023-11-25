<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message\Proxy;

use Imi\RequestContextProxy\BaseRequestContextProxy;

/**
 * @method        \Imi\Util\Socket\IPEndPoint              getClientAddress()
 * @method static \Imi\Util\Socket\IPEndPoint              getClientAddress()
 * @method        mixed                                    getServerParam(string $name, mixed $default = NULL)
 * @method static mixed                                    getServerParam(string $name, mixed $default = NULL)
 * @method        string|null                              getCookie(string $name, ?string $default = NULL)
 * @method static string|null                              getCookie(string $name, ?string $default = NULL)
 * @method        \Imi\Util\Http\Contract\IServerRequest   setCookieParams(array $cookies)
 * @method static \Imi\Util\Http\Contract\IServerRequest   setCookieParams(array $cookies)
 * @method        \Imi\Util\Http\Contract\IServerRequest   setQueryParams(array $query)
 * @method static \Imi\Util\Http\Contract\IServerRequest   setQueryParams(array $query)
 * @method        \Imi\Util\Http\Contract\IServerRequest   setUploadedFiles(array $uploadedFiles)
 * @method static \Imi\Util\Http\Contract\IServerRequest   setUploadedFiles(array $uploadedFiles)
 * @method        \Imi\Util\Http\Contract\IServerRequest   setParsedBody(object|array|null $data)
 * @method static \Imi\Util\Http\Contract\IServerRequest   setParsedBody(object|array|null $data)
 * @method        \Imi\Util\Http\Contract\IServerRequest   setAttribute(string $name, mixed $value)
 * @method static \Imi\Util\Http\Contract\IServerRequest   setAttribute(string $name, mixed $value)
 * @method        \Imi\Util\Http\Contract\IServerRequest   removeAttribute(string $name)
 * @method static \Imi\Util\Http\Contract\IServerRequest   removeAttribute(string $name)
 * @method        mixed                                    get(?string $name = NULL, mixed $default = NULL)
 * @method static mixed                                    get(?string $name = NULL, mixed $default = NULL)
 * @method        mixed                                    post(?string $name = NULL, mixed $default = NULL)
 * @method static mixed                                    post(?string $name = NULL, mixed $default = NULL)
 * @method        bool                                     hasGet(string $name)
 * @method static bool                                     hasGet(string $name)
 * @method        bool                                     hasPost(string $name)
 * @method static bool                                     hasPost(string $name)
 * @method        mixed                                    request(?string $name = NULL, mixed $default = NULL)
 * @method static mixed                                    request(?string $name = NULL, mixed $default = NULL)
 * @method        bool                                     hasRequest(string $name)
 * @method static bool                                     hasRequest(string $name)
 * @method        \Imi\Util\Http\Contract\IServerRequest   withGet(array $get)
 * @method static \Imi\Util\Http\Contract\IServerRequest   withGet(array $get)
 * @method        \Imi\Util\Http\Contract\IServerRequest   setGet(array $get)
 * @method static \Imi\Util\Http\Contract\IServerRequest   setGet(array $get)
 * @method        \Imi\Util\Http\Contract\IServerRequest   withPost(mixed $post)
 * @method static \Imi\Util\Http\Contract\IServerRequest   withPost(mixed $post)
 * @method        \Imi\Util\Http\Contract\IServerRequest   setPost(mixed $post)
 * @method static \Imi\Util\Http\Contract\IServerRequest   setPost(mixed $post)
 * @method        \Imi\Util\Http\Contract\IServerRequest   withRequest(array $request)
 * @method static \Imi\Util\Http\Contract\IServerRequest   withRequest(array $request)
 * @method        \Imi\Util\Http\Contract\IServerRequest   setRequest(array $request)
 * @method static \Imi\Util\Http\Contract\IServerRequest   setRequest(array $request)
 * @method        array                                    getServerParams()
 * @method static array                                    getServerParams()
 * @method        array                                    getCookieParams()
 * @method static array                                    getCookieParams()
 * @method        \Psr\Http\Message\ServerRequestInterface withCookieParams(array $cookies)
 * @method static \Psr\Http\Message\ServerRequestInterface withCookieParams(array $cookies)
 * @method        array                                    getQueryParams()
 * @method static array                                    getQueryParams()
 * @method        \Psr\Http\Message\ServerRequestInterface withQueryParams(array $query)
 * @method static \Psr\Http\Message\ServerRequestInterface withQueryParams(array $query)
 * @method        array                                    getUploadedFiles()
 * @method static array                                    getUploadedFiles()
 * @method        \Psr\Http\Message\ServerRequestInterface withUploadedFiles(array $uploadedFiles)
 * @method static \Psr\Http\Message\ServerRequestInterface withUploadedFiles(array $uploadedFiles)
 * @method        array|object|null                        getParsedBody()
 * @method static null|array|object                        getParsedBody()
 * @method        \Psr\Http\Message\ServerRequestInterface withParsedBody($data)
 * @method static \Psr\Http\Message\ServerRequestInterface withParsedBody($data)
 * @method        array                                    getAttributes()
 * @method static array                                    getAttributes()
 * @method        mixed                                    getAttribute(string $name, $default = NULL)
 * @method static mixed                                    getAttribute(string $name, $default = NULL)
 * @method        \Psr\Http\Message\ServerRequestInterface withAttribute(string $name, $value)
 * @method static \Psr\Http\Message\ServerRequestInterface withAttribute(string $name, $value)
 * @method        \Psr\Http\Message\ServerRequestInterface withoutAttribute(string $name)
 * @method static \Psr\Http\Message\ServerRequestInterface withoutAttribute(string $name)
 * @method        string                                   getRequestTarget()
 * @method static string                                   getRequestTarget()
 * @method        \Psr\Http\Message\RequestInterface       withRequestTarget(string $requestTarget)
 * @method static \Psr\Http\Message\RequestInterface       withRequestTarget(string $requestTarget)
 * @method        string                                   getMethod()
 * @method static string                                   getMethod()
 * @method        \Psr\Http\Message\RequestInterface       withMethod(string $method)
 * @method static \Psr\Http\Message\RequestInterface       withMethod(string $method)
 * @method        \Psr\Http\Message\UriInterface           getUri()
 * @method static \Psr\Http\Message\UriInterface           getUri()
 * @method        \Psr\Http\Message\RequestInterface       withUri(\Psr\Http\Message\UriInterface $uri, bool $preserveHost = false)
 * @method static \Psr\Http\Message\RequestInterface       withUri(\Psr\Http\Message\UriInterface $uri, bool $preserveHost = false)
 * @method        string                                   getProtocolVersion()
 * @method static string                                   getProtocolVersion()
 * @method        \Psr\Http\Message\MessageInterface       withProtocolVersion(string $version)
 * @method static \Psr\Http\Message\MessageInterface       withProtocolVersion(string $version)
 * @method        string[][]                               getHeaders()
 * @method static string[][]                               getHeaders()
 * @method        bool                                     hasHeader(string $name)
 * @method static bool                                     hasHeader(string $name)
 * @method        string[]                                 getHeader(string $name)
 * @method static string[]                                 getHeader(string $name)
 * @method        string                                   getHeaderLine(string $name)
 * @method static string                                   getHeaderLine(string $name)
 * @method        \Psr\Http\Message\MessageInterface       withHeader(string $name, $value)
 * @method static \Psr\Http\Message\MessageInterface       withHeader(string $name, $value)
 * @method        \Psr\Http\Message\MessageInterface       withAddedHeader(string $name, $value)
 * @method static \Psr\Http\Message\MessageInterface       withAddedHeader(string $name, $value)
 * @method        \Psr\Http\Message\MessageInterface       withoutHeader(string $name)
 * @method static \Psr\Http\Message\MessageInterface       withoutHeader(string $name)
 * @method        \Psr\Http\Message\StreamInterface        getBody()
 * @method static \Psr\Http\Message\StreamInterface        getBody()
 * @method        \Psr\Http\Message\MessageInterface       withBody(\Psr\Http\Message\StreamInterface $body)
 * @method static \Psr\Http\Message\MessageInterface       withBody(\Psr\Http\Message\StreamInterface $body)
 * @method        \Imi\Util\Http\Contract\IRequest         setRequestTarget(string $requestTarget)
 * @method static \Imi\Util\Http\Contract\IRequest         setRequestTarget(string $requestTarget)
 * @method        \Imi\Util\Http\Contract\IRequest         setMethod(string $method)
 * @method static \Imi\Util\Http\Contract\IRequest         setMethod(string $method)
 * @method        \Imi\Util\Http\Contract\IRequest         setUri(\Psr\Http\Message\UriInterface $uri, bool $preserveHost = false)
 * @method static \Imi\Util\Http\Contract\IRequest         setUri(\Psr\Http\Message\UriInterface $uri, bool $preserveHost = false)
 * @method        \Psr\Http\Message\UriInterface           getAppUri(?string $serverName = NULL)
 * @method static \Psr\Http\Message\UriInterface           getAppUri(?string $serverName = NULL)
 */
#[
    \Imi\RequestContextProxy\Annotation\RequestContextProxy(class: \Imi\Server\Http\Message\Contract\IHttpRequest::class, name: 'request'),
    \Imi\Bean\Annotation\Bean(name: 'HttpRequestProxy')
]
class RequestProxyObject extends BaseRequestContextProxy implements \Imi\Server\Http\Message\Contract\IHttpRequest
{
    /**
     * {@inheritDoc}
     */
    public function getClientAddress(): \Imi\Util\Socket\IPEndPoint
    {
        return self::__getProxyInstance()->getClientAddress(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getServerParam(string $name, mixed $default = null): mixed
    {
        return self::__getProxyInstance()->getServerParam($name, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function getCookie(string $name, ?string $default = null): ?string
    {
        return self::__getProxyInstance()->getCookie($name, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function setCookieParams(array $cookies): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->setCookieParams($cookies);
    }

    /**
     * {@inheritDoc}
     */
    public function setQueryParams(array $query): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->setQueryParams($query);
    }

    /**
     * {@inheritDoc}
     */
    public function setUploadedFiles(array $uploadedFiles): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->setUploadedFiles($uploadedFiles);
    }

    /**
     * {@inheritDoc}
     */
    public function setParsedBody(object|array|null $data): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->setParsedBody($data);
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute(string $name, mixed $value): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->setAttribute($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function removeAttribute(string $name): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->removeAttribute($name);
    }

    /**
     * {@inheritDoc}
     */
    public function get(?string $name = null, mixed $default = null): mixed
    {
        return self::__getProxyInstance()->get($name, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function post(?string $name = null, mixed $default = null): mixed
    {
        return self::__getProxyInstance()->post($name, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function hasGet(string $name): bool
    {
        return self::__getProxyInstance()->hasGet($name);
    }

    /**
     * {@inheritDoc}
     */
    public function hasPost(string $name): bool
    {
        return self::__getProxyInstance()->hasPost($name);
    }

    /**
     * {@inheritDoc}
     */
    public function request(?string $name = null, mixed $default = null): mixed
    {
        return self::__getProxyInstance()->request($name, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function hasRequest(string $name): bool
    {
        return self::__getProxyInstance()->hasRequest($name);
    }

    /**
     * {@inheritDoc}
     */
    public function withGet(array $get): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->withGet($get);
    }

    /**
     * {@inheritDoc}
     */
    public function setGet(array $get): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->setGet($get);
    }

    /**
     * {@inheritDoc}
     */
    public function withPost(mixed $post): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->withPost($post);
    }

    /**
     * {@inheritDoc}
     */
    public function setPost(mixed $post): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->setPost($post);
    }

    /**
     * {@inheritDoc}
     */
    public function withRequest(array $request): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->withRequest($request);
    }

    /**
     * {@inheritDoc}
     */
    public function setRequest(array $request): \Imi\Util\Http\Contract\IServerRequest
    {
        return self::__getProxyInstance()->setRequest($request);
    }

    /**
     * {@inheritDoc}
     */
    public function getServerParams(): array
    {
        return self::__getProxyInstance()->getServerParams(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getCookieParams(): array
    {
        return self::__getProxyInstance()->getCookieParams(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withCookieParams(array $cookies): \Psr\Http\Message\ServerRequestInterface
    {
        return self::__getProxyInstance()->withCookieParams($cookies);
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryParams(): array
    {
        return self::__getProxyInstance()->getQueryParams(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withQueryParams(array $query): \Psr\Http\Message\ServerRequestInterface
    {
        return self::__getProxyInstance()->withQueryParams($query);
    }

    /**
     * {@inheritDoc}
     */
    public function getUploadedFiles(): array
    {
        return self::__getProxyInstance()->getUploadedFiles(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withUploadedFiles(array $uploadedFiles): \Psr\Http\Message\ServerRequestInterface
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
    public function withParsedBody($data): \Psr\Http\Message\ServerRequestInterface
    {
        return self::__getProxyInstance()->withParsedBody($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes(): array
    {
        return self::__getProxyInstance()->getAttributes(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute(string $name, $default = null)
    {
        return self::__getProxyInstance()->getAttribute($name, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function withAttribute(string $name, $value): \Psr\Http\Message\ServerRequestInterface
    {
        return self::__getProxyInstance()->withAttribute($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function withoutAttribute(string $name): \Psr\Http\Message\ServerRequestInterface
    {
        return self::__getProxyInstance()->withoutAttribute($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestTarget(): string
    {
        return self::__getProxyInstance()->getRequestTarget(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withRequestTarget(string $requestTarget): \Psr\Http\Message\RequestInterface
    {
        return self::__getProxyInstance()->withRequestTarget($requestTarget);
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod(): string
    {
        return self::__getProxyInstance()->getMethod(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withMethod(string $method): \Psr\Http\Message\RequestInterface
    {
        return self::__getProxyInstance()->withMethod($method);
    }

    /**
     * {@inheritDoc}
     */
    public function getUri(): \Psr\Http\Message\UriInterface
    {
        return self::__getProxyInstance()->getUri(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withUri(\Psr\Http\Message\UriInterface $uri, bool $preserveHost = false): \Psr\Http\Message\RequestInterface
    {
        return self::__getProxyInstance()->withUri($uri, $preserveHost);
    }

    /**
     * {@inheritDoc}
     */
    public function getProtocolVersion(): string
    {
        return self::__getProxyInstance()->getProtocolVersion(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withProtocolVersion(string $version): \Psr\Http\Message\MessageInterface
    {
        return self::__getProxyInstance()->withProtocolVersion($version);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders(): array
    {
        return self::__getProxyInstance()->getHeaders(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function hasHeader(string $name): bool
    {
        return self::__getProxyInstance()->hasHeader($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeader(string $name): array
    {
        return self::__getProxyInstance()->getHeader($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaderLine(string $name): string
    {
        return self::__getProxyInstance()->getHeaderLine($name);
    }

    /**
     * {@inheritDoc}
     */
    public function withHeader(string $name, $value): \Psr\Http\Message\MessageInterface
    {
        return self::__getProxyInstance()->withHeader($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function withAddedHeader(string $name, $value): \Psr\Http\Message\MessageInterface
    {
        return self::__getProxyInstance()->withAddedHeader($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function withoutHeader(string $name): \Psr\Http\Message\MessageInterface
    {
        return self::__getProxyInstance()->withoutHeader($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getBody(): \Psr\Http\Message\StreamInterface
    {
        return self::__getProxyInstance()->getBody(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withBody(\Psr\Http\Message\StreamInterface $body): \Psr\Http\Message\MessageInterface
    {
        return self::__getProxyInstance()->withBody($body);
    }

    /**
     * {@inheritDoc}
     */
    public function setRequestTarget(string $requestTarget): \Imi\Util\Http\Contract\IRequest
    {
        return self::__getProxyInstance()->setRequestTarget($requestTarget);
    }

    /**
     * {@inheritDoc}
     */
    public function setMethod(string $method): \Imi\Util\Http\Contract\IRequest
    {
        return self::__getProxyInstance()->setMethod($method);
    }

    /**
     * {@inheritDoc}
     */
    public function setUri(\Psr\Http\Message\UriInterface $uri, bool $preserveHost = false): \Imi\Util\Http\Contract\IRequest
    {
        return self::__getProxyInstance()->setUri($uri, $preserveHost);
    }

    /**
     * {@inheritDoc}
     */
    public function getAppUri(?string $serverName = null): \Psr\Http\Message\UriInterface
    {
        return self::__getProxyInstance()->getAppUri($serverName);
    }
}
