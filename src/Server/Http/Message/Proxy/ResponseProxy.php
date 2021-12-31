<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message\Proxy;

use Imi\RequestContextProxy\Annotation\RequestContextProxy;
use Imi\RequestContextProxy\BaseRequestContextProxy;

/**
 * @RequestContextProxy(class="Imi\Server\Http\Message\Contract\IHttpResponse", name="response")
 *
 * @method static \Imi\Server\Http\Message\Contract\IHttpResponse redirect(string $url, int $status = 302)
 * @method static \Imi\Server\Http\Message\Contract\IHttpResponse send()
 * @method static \Imi\Server\Http\Message\Contract\IHttpResponse sendFile(string $filename, int $offset = 0, int $length = 0)
 * @method bool   isWritable()
 * @method static bool isWritable()
 * @method static setStatus(int $code, string $reasonPhrase = '')
 * @method static static setStatus(int $code, string $reasonPhrase = '')
 * @method static withCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
 * @method static static withCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
 * @method static setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
 * @method static static setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
 * @method static array getCookieParams()
 * @method static mixed getCookie(string $name, $default = NULL)
 * @method static array getTrailers()
 * @method static bool hasTrailer(string $name)
 * @method static string|null getTrailer(string $name)
 * @method static withTrailer(string $name, string $value)
 * @method static static withTrailer(string $name, string $value)
 * @method static setTrailer(string $name, string $value)
 * @method static static setTrailer(string $name, string $value)
 * @method static int getStatusCode()
 * @method static withStatus($code, $reasonPhrase = '')
 * @method static static withStatus($code, $reasonPhrase = '')
 * @method static string getReasonPhrase()
 * @method static string getProtocolVersion()
 * @method static withProtocolVersion($version)
 * @method static static withProtocolVersion($version)
 * @method static string[][] getHeaders()
 * @method static bool hasHeader($name)
 * @method static string[] getHeader($name)
 * @method static string getHeaderLine($name)
 * @method static withHeader($name, $value)
 * @method static static withHeader($name, $value)
 * @method static withAddedHeader($name, $value)
 * @method static static withAddedHeader($name, $value)
 * @method static withoutHeader($name)
 * @method static static withoutHeader($name)
 * @method static \Psr\Http\Message\StreamInterface getBody()
 * @method static withBody(\Psr\Http\Message\StreamInterface $body)
 * @method static static withBody(\Psr\Http\Message\StreamInterface $body)
 * @method static setProtocolVersion(string $version)
 * @method static static setProtocolVersion(string $version)
 * @method static setHeader(string $name, $value)
 * @method static static setHeader(string $name, $value)
 * @method static addHeader(string $name, $value)
 * @method static static addHeader(string $name, $value)
 * @method static removeHeader(string $name)
 * @method static static removeHeader(string $name)
 * @method static setBody(\Psr\Http\Message\StreamInterface $body)
 * @method static static setBody(\Psr\Http\Message\StreamInterface $body)
 */
class ResponseProxy extends BaseRequestContextProxy
{
}
