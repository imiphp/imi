<?php

declare(strict_types=1);

namespace Imi\Util\Http\Contract;

use Psr\Http\Message\ResponseInterface;

interface IResponse extends ResponseInterface, IMessage
{
    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @param int    $code         the 3-digit integer result code to set
     * @param string $reasonPhrase the reason phrase to use with the
     *                             provided status code; if none is provided, implementations MAY
     *                             use the defaults as suggested in the HTTP specification
     *
     * @return static
     *
     * @throws \InvalidArgumentException for invalid status code arguments
     */
    public function setStatus(int $code, string $reasonPhrase = ''): self;

    /**
     * 设置cookie.
     *
     * @param string $key
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     *
     * @return static
     */
    public function withCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self;

    /**
     * 设置cookie.
     *
     * @param string $key
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     *
     * @return static
     */
    public function setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self;

    /**
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     *
     * The data MUST be compatible with the structure of the $_COOKIE
     * superglobal.
     *
     * @return array
     */
    public function getCookieParams(): array;

    /**
     * 获取cookie值
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getCookie(string $name, $default = null);

    /**
     * 获取 Trailer 列表.
     *
     * @return array
     */
    public function getTrailers(): array;

    /**
     * Trailer 是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasTrailer(string $name): bool;

    /**
     * 获取 Trailer 值
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getTrailer(string $name): ?string;

    /**
     * 设置 Trailer.
     *
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function withTrailer(string $name, string $value): self;

    /**
     * 设置 Trailer.
     *
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function setTrailer(string $name, string $value): self;
}
