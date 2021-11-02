<?php

declare(strict_types=1);

namespace Imi\Util\Http;

use Imi\Util\Http\Consts\RequestMethod;
use Imi\Util\Http\Contract\IRequest;
use Imi\Util\Uri;
use Psr\Http\Message\UriInterface;

class Request extends AbstractMessage implements IRequest
{
    /**
     * 请求地址
     */
    protected UriInterface $uri;

    /**
     * 请求方法.
     */
    protected string $method = RequestMethod::GET;

    /**
     * uri 是否初始化.
     */
    protected bool $uriInited = false;

    /**
     * method 是否初始化.
     */
    protected bool $methodInited = false;

    /**
     * {@inheritDoc}
     */
    public function getRequestTarget()
    {
        return (string) $this->uri;
    }

    /**
     * {@inheritDoc}
     */
    public function withRequestTarget($requestTarget)
    {
        $self = clone $this;
        $self->withUri(new Uri($requestTarget));

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function setRequestTarget($requestTarget): self
    {
        $this->setUri(new Uri($requestTarget));

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function initMethod(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod()
    {
        if (!$this->methodInited)
        {
            $this->initMethod();
            $this->methodInited = true;
        }

        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public function withMethod($method)
    {
        $self = clone $this;
        $self->method = $method;
        $self->methodInited = true;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        $this->methodInited = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function initUri(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getUri()
    {
        if (!$this->uriInited)
        {
            $this->initUri();
            $this->uriInited = true;
        }

        return $this->uri;
    }

    /**
     * {@inheritDoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $self = clone $this;

        // @phpstan-ignore-next-line
        return $self->setUri($uri, $preserveHost);
    }

    /**
     * {@inheritDoc}
     */
    public function setUri(UriInterface $uri, bool $preserveHost = false): self
    {
        $this->uri = $uri;
        $this->uriInited = true;
        if (!$preserveHost)
        {
            $this->headers = [];
            $this->headerNames = [];
            $this->headersInited = true;
        }

        return $this;
    }
}
