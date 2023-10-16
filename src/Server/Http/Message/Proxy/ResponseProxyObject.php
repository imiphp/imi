<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message\Proxy;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;
use Imi\RequestContextProxy\BaseRequestContextProxy;

/**
 * @Bean(name="HttpResponseProxy", recursion=false, instanceType="singleton")
 *
 * @RequestContextProxy(class="Imi\Server\Http\Message\Contract\IHttpResponse", name="response")
 *
 * @method        static                                                              redirect(string $url, int $status = 302)
 * @method static static                                                              redirect(string $url, int $status = 302)
 * @method        static                                                              send()
 * @method static static                                                              send()
 * @method        static                                                              sendFile(string $filename, ?string $contentType = NULL, ?string $outputFileName = NULL, int $offset = 0, int $length = 0)
 * @method static static                                                              sendFile(string $filename, ?string $contentType = NULL, ?string $outputFileName = NULL, int $offset = 0, int $length = 0)
 * @method        bool                                                                isHeaderWritable()
 * @method static bool                                                                isHeaderWritable()
 * @method        bool                                                                isBodyWritable()
 * @method static bool                                                                isBodyWritable()
 * @method        static                                                              withResponseBodyEmitter(?\Imi\Server\Http\Message\Emitter\Contract\IResponseBodyEmitter $responseBodyEmitter)
 * @method static static                                                              withResponseBodyEmitter(?\Imi\Server\Http\Message\Emitter\Contract\IResponseBodyEmitter $responseBodyEmitter)
 * @method        static                                                              setResponseBodyEmitter(?\Imi\Server\Http\Message\Emitter\Contract\IResponseBodyEmitter $responseBodyEmitter)
 * @method static static                                                              setResponseBodyEmitter(?\Imi\Server\Http\Message\Emitter\Contract\IResponseBodyEmitter $responseBodyEmitter)
 * @method        \Imi\Server\Http\Message\Emitter\Contract\IResponseBodyEmitter|null getResponseBodyEmitter()
 * @method static \Imi\Server\Http\Message\Emitter\Contract\IResponseBodyEmitter|null getResponseBodyEmitter()
 * @method        static                                                              setStatus(int $code, string $reasonPhrase = '')
 * @method static static                                                              setStatus(int $code, string $reasonPhrase = '')
 * @method        static                                                              withCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
 * @method static static                                                              withCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
 * @method        static                                                              setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
 * @method static static                                                              setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
 * @method        array                                                               getCookieParams()
 * @method static array                                                               getCookieParams()
 * @method        array|null                                                          getCookie(string $name, ?array $default = NULL)
 * @method static array|null                                                          getCookie(string $name, ?array $default = NULL)
 * @method        array                                                               getTrailers()
 * @method static array                                                               getTrailers()
 * @method        bool                                                                hasTrailer(string $name)
 * @method static bool                                                                hasTrailer(string $name)
 * @method        string|null                                                         getTrailer(string $name)
 * @method static string|null                                                         getTrailer(string $name)
 * @method        static                                                              withTrailer(string $name, string $value)
 * @method static static                                                              withTrailer(string $name, string $value)
 * @method        static                                                              setTrailer(string $name, string $value)
 * @method static static                                                              setTrailer(string $name, string $value)
 * @method        int                                                                 getStatusCode()
 * @method static int                                                                 getStatusCode()
 * @method        static                                                              withStatus(int $code, string $reasonPhrase = '')
 * @method static static                                                              withStatus(int $code, string $reasonPhrase = '')
 * @method        string                                                              getReasonPhrase()
 * @method static string                                                              getReasonPhrase()
 * @method        string                                                              getProtocolVersion()
 * @method static string                                                              getProtocolVersion()
 * @method        static                                                              withProtocolVersion(string $version)
 * @method static static                                                              withProtocolVersion(string $version)
 * @method        string[][]                                                          getHeaders()
 * @method static string[][]                                                          getHeaders()
 * @method        bool                                                                hasHeader(string $name)
 * @method static bool                                                                hasHeader(string $name)
 * @method        string[]                                                            getHeader(string $name)
 * @method static string[]                                                            getHeader(string $name)
 * @method        string                                                              getHeaderLine(string $name)
 * @method static string                                                              getHeaderLine(string $name)
 * @method        static                                                              withHeader(string $name, $value)
 * @method static static                                                              withHeader(string $name, $value)
 * @method        static                                                              withAddedHeader(string $name, $value)
 * @method static static                                                              withAddedHeader(string $name, $value)
 * @method        static                                                              withoutHeader(string $name)
 * @method static static                                                              withoutHeader(string $name)
 * @method        \Psr\Http\Message\StreamInterface                                   getBody()
 * @method static \Psr\Http\Message\StreamInterface                                   getBody()
 * @method        static                                                              withBody(\Psr\Http\Message\StreamInterface $body)
 * @method static static                                                              withBody(\Psr\Http\Message\StreamInterface $body)
 * @method        static                                                              setProtocolVersion(string $version)
 * @method static static                                                              setProtocolVersion(string $version)
 * @method        static                                                              setHeader(string $name, array|string $value)
 * @method static static                                                              setHeader(string $name, array|string $value)
 * @method        static                                                              addHeader(string $name, array|string $value)
 * @method static static                                                              addHeader(string $name, array|string $value)
 * @method        static                                                              removeHeader(string $name)
 * @method static static                                                              removeHeader(string $name)
 * @method        static                                                              setBody(\Psr\Http\Message\StreamInterface $body)
 * @method static static                                                              setBody(\Psr\Http\Message\StreamInterface $body)
 */
class ResponseProxyObject extends BaseRequestContextProxy implements \Imi\Server\Http\Message\Contract\IHttpResponse
{
    /**
     * {@inheritDoc}
     */
    public function redirect(string $url, int $status = 302): static
    {
        return self::__getProxyInstance()->redirect($url, $status);
    }

    /**
     * {@inheritDoc}
     */
    public function send(): static
    {
        return self::__getProxyInstance()->send(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function sendFile(string $filename, ?string $contentType = null, ?string $outputFileName = null, int $offset = 0, int $length = 0): static
    {
        return self::__getProxyInstance()->sendFile($filename, $contentType, $outputFileName, $offset, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function isHeaderWritable(): bool
    {
        return self::__getProxyInstance()->isHeaderWritable(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function isBodyWritable(): bool
    {
        return self::__getProxyInstance()->isBodyWritable(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withResponseBodyEmitter(?\Imi\Server\Http\Message\Emitter\Contract\IResponseBodyEmitter $responseBodyEmitter): static
    {
        return self::__getProxyInstance()->withResponseBodyEmitter($responseBodyEmitter);
    }

    /**
     * {@inheritDoc}
     */
    public function setResponseBodyEmitter(?\Imi\Server\Http\Message\Emitter\Contract\IResponseBodyEmitter $responseBodyEmitter): static
    {
        return self::__getProxyInstance()->setResponseBodyEmitter($responseBodyEmitter);
    }

    /**
     * {@inheritDoc}
     */
    public function getResponseBodyEmitter(): ?\Imi\Server\Http\Message\Emitter\Contract\IResponseBodyEmitter
    {
        return self::__getProxyInstance()->getResponseBodyEmitter(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus(int $code, string $reasonPhrase = ''): static
    {
        return self::__getProxyInstance()->setStatus($code, $reasonPhrase);
    }

    /**
     * {@inheritDoc}
     */
    public function withCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): static
    {
        return self::__getProxyInstance()->withCookie($key, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * {@inheritDoc}
     */
    public function setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): static
    {
        return self::__getProxyInstance()->setCookie($key, $value, $expire, $path, $domain, $secure, $httponly);
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
    public function getCookie(string $name, ?array $default = null): ?array
    {
        return self::__getProxyInstance()->getCookie($name, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function getTrailers(): array
    {
        return self::__getProxyInstance()->getTrailers(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function hasTrailer(string $name): bool
    {
        return self::__getProxyInstance()->hasTrailer($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getTrailer(string $name): ?string
    {
        return self::__getProxyInstance()->getTrailer($name);
    }

    /**
     * {@inheritDoc}
     */
    public function withTrailer(string $name, string $value): static
    {
        return self::__getProxyInstance()->withTrailer($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function setTrailer(string $name, string $value): static
    {
        return self::__getProxyInstance()->setTrailer($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusCode(): int
    {
        return self::__getProxyInstance()->getStatusCode(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function withStatus(int $code, string $reasonPhrase = ''): \Psr\Http\Message\ResponseInterface
    {
        return self::__getProxyInstance()->withStatus($code, $reasonPhrase);
    }

    /**
     * {@inheritDoc}
     */
    public function getReasonPhrase(): string
    {
        return self::__getProxyInstance()->getReasonPhrase(...\func_get_args());
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
    public function setProtocolVersion(string $version): static
    {
        return self::__getProxyInstance()->setProtocolVersion($version);
    }

    /**
     * {@inheritDoc}
     */
    public function setHeader(string $name, array|string $value): static
    {
        return self::__getProxyInstance()->setHeader($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function addHeader(string $name, array|string $value): static
    {
        return self::__getProxyInstance()->addHeader($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function removeHeader(string $name): static
    {
        return self::__getProxyInstance()->removeHeader($name);
    }

    /**
     * {@inheritDoc}
     */
    public function setBody(\Psr\Http\Message\StreamInterface $body): static
    {
        return self::__getProxyInstance()->setBody($body);
    }
}
