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
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function withStatus(int $code, string $reasonPhrase = ''): self
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
     *
     * @return static
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
    public function getReasonPhrase(): string
    {
        if ('' === $this->reasonPhrase)
        {
            return $this->reasonPhrase = StatusCode::getReasonPhrase($this->statusCode);
        }

        return $this->reasonPhrase;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
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
     *
     * @return static
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
    public function getCookie(string $name, ?array $default = null): ?array
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
     * {@inheritDoc}
     *
     * @return static
     */
    public function setTrailer(string $name, string $value): self
    {
        $this->trailers[$name] = $value;

        return $this;
    }
}
