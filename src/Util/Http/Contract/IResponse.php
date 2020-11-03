<?php

namespace Imi\Util\Http\Contract;

use Imi\Util\Http\Consts\StatusCode;
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
     * @param int    $code         The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *                             provided status code; if none is provided, implementations MAY
     *                             use the defaults as suggested in the HTTP specification.
     *
     * @return static
     *
     * @throws \InvalidArgumentException For invalid status code arguments.
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

    /**
     * 设置服务器端重定向
     * 默认状态码为302.
     *
     * @param string $url
     * @param int    $status
     *
     * @return static
     */
    public function redirect(string $url, int $status = StatusCode::FOUND): self;

    /**
     * 发送文件，一般用于文件下载.
     *
     * @param string $filename 要发送的文件名称，文件不存在或没有访问权限sendfile会失败
     * @param int    $offset   上传文件的偏移量，可以指定从文件的中间部分开始传输数据。此特性可用于支持断点续传。
     * @param int    $length   发送数据的尺寸，默认为整个文件的尺寸
     *
     * @return static
     */
    public function sendFile(string $filename, int $offset = 0, int $length = 0): self;

    /**
     * 获取发送文件参数.
     *
     * @return array
     */
    public function getSendFile(): array;
}
