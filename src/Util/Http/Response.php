<?php

namespace Imi\Util\Http;

use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Http\Contract\IResponse;
use Imi\Util\Stream\MemoryStream;

class Response extends AbstractMessage implements IResponse
{
    /**
     * 状态码
     *
     * @var int
     */
    protected int $statusCode;

    /**
     * 状态码原因短语.
     *
     * @var string
     */
    protected string $reasonPhrase;

    /**
     * Trailer 列表.
     *
     * @var array
     */
    protected array $trailers = [];

    /**
     * cookie数据.
     *
     * @var array
     */
    protected array $cookies = [];

    /**
     * 发送文件参数.
     *
     * @var array
     */
    protected array $sendFile = [];

    public function __construct()
    {
        $this->statusCode = $statusCode = StatusCode::OK;
        $this->reasonPhrase = StatusCode::getReasonPhrase($statusCode);
        $this->body = new MemoryStream();
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
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
    public function withStatus($code, $reasonPhrase = '')
    {
        $self = clone $this;
        $self->statusCode = $code;
        if ('' === $reasonPhrase)
        {
            $self->reasonPhrase = StatusCode::getReasonPhrase($code);
        }
        else
        {
            $self->reasonPhrase = $reasonPhrase;
        }

        return $self;
    }

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
    public function setStatus(int $code, string $reasonPhrase = ''): self
    {
        $this->statusCode = $code;
        if ('' === $reasonPhrase)
        {
            $this->reasonPhrase = StatusCode::getReasonPhrase($code);
        }
        else
        {
            $this->reasonPhrase = $reasonPhrase;
        }

        return $this;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @return string reason phrase; must return an empty string if none present
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

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
    public function withCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self
    {
        $self = clone $this;
        $self->cookies[] = [
            'key'       => $key,
            'value'     => $value,
            'expire'    => $expire,
            'path'      => $path,
            'domain'    => $domain,
            'secure'    => $secure,
            'httponly'  => $httponly,
        ];

        return $self;
    }

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
    public function setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self
    {
        $this->cookies[] = [
            'key'       => $key,
            'value'     => $value,
            'expire'    => $expire,
            'path'      => $path,
            'domain'    => $domain,
            'secure'    => $secure,
            'httponly'  => $httponly,
        ];

        return $this;
    }

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
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    /**
     * 获取cookie值
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getCookie(string $name, $default = null)
    {
        return $this->cookies[$name] ?? $default;
    }

    /**
     * 获取 Trailer 列表.
     *
     * @return array
     */
    public function getTrailers(): array
    {
        return $this->trailers;
    }

    /**
     * Trailer 是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasTrailer(string $name): bool
    {
        return isset($this->trailers[$name]);
    }

    /**
     * 获取 Trailer 值
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getTrailer(string $name): ?string
    {
        return $this->trailers[$name] ?? null;
    }

    /**
     * 设置 Trailer.
     *
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function withTrailer(string $name, string $value): self
    {
        $self = clone $this;
        $self->trailers[$name] = $value;

        return $self;
    }

    /**
     * 设置 Trailer.
     *
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function setTrailer(string $name, string $value): self
    {
        $this->trailers[$name] = $value;

        return $this;
    }
}
