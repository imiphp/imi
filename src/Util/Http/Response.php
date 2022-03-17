<?php

declare(strict_types=1);

namespace Imi\Util\Http;

use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Http\Contract\IResponse;
use Imi\Util\Stream\MemoryStream;

class Response extends AbstractMessage implements IResponse
{
    /**
     * 状态码
     */
    protected int $statusCode = StatusCode::OK;

    /**
     * 状态码原因短语.
     */
    protected string $reasonPhrase = '';

    /**
     * Trailer 列表.
     */
    protected array $trailers = [];

    /**
     * cookie数据.
     */
    protected array $cookies = [];

    /**
     * 发送文件参数.
     */
    protected array $sendFile = [];

    public function __construct()
    {
        $this->body = new MemoryStream();
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getReasonPhrase()
    {
        if ('' === $this->reasonPhrase)
        {
            return $this->reasonPhrase = StatusCode::getReasonPhrase($this->statusCode);
        }

        return $this->reasonPhrase;
    }

    /**
     * {@inheritDoc}
     */
    public function withCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self
    {
        $self = clone $this;
        $self->cookies[$key] = [
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
     * {@inheritDoc}
     */
    public function setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self
    {
        $this->cookies[$key] = [
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
     * {@inheritDoc}
     */
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    /**
     * {@inheritDoc}
     */
    public function getCookie(string $name, $default = null)
    {
        return $this->cookies[$name] ?? $default;
    }

    /**
     * {@inheritDoc}
     */
    public function getTrailers(): array
    {
        return $this->trailers;
    }

    /**
     * {@inheritDoc}
     */
    public function hasTrailer(string $name): bool
    {
        return isset($this->trailers[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function getTrailer(string $name): ?string
    {
        return $this->trailers[$name] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function withTrailer(string $name, string $value): self
    {
        $self = clone $this;
        $self->trailers[$name] = $value;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function setTrailer(string $name, string $value): self
    {
        $this->trailers[$name] = $value;

        return $this;
    }
}
