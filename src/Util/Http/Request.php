<?php

declare(strict_types=1);

namespace Imi\Util\Http;

use Imi\Server\Server;
use Imi\Util\Http\Consts\RequestMethod;
use Imi\Util\Http\Contract\IRequest;
use Imi\Util\Uri;
use Psr\Http\Message\UriInterface;

class Request extends AbstractMessage implements IRequest
{
    /**
     * 请求地址
     */
    protected ?UriInterface $uri = null;

    /**
     * 请求地址
     */
    protected ?UriInterface $appUri = null;

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
     * 请求目标.
     */
    protected ?string $requestTarget = null;

    /**
     * {@inheritDoc}
     */
    public function getRequestTarget(): string
    {
        if (null === $this->requestTarget)
        {
            $uri = $this->getUri();
            $requestTarget = $uri->getPath();
            if ('' !== ($query = $uri->getQuery()))
            {
                $requestTarget .= '?' . $query;
            }

            return $requestTarget;
        }

        return $this->requestTarget;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function withRequestTarget(string $requestTarget): self
    {
        $self = clone $this;
        $self->requestTarget = $requestTarget;

        return $self;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function setRequestTarget(string $requestTarget): self
    {
        $this->requestTarget = $requestTarget;

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
    public function getMethod(): string
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
     *
     * @return static
     */
    public function withMethod(string $method): self
    {
        $self = clone $this;
        $self->method = $method;
        $self->methodInited = true;

        return $self;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
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
    public function getUri(): UriInterface
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
     *
     * @return static
     */
    public function withUri(UriInterface $uri, bool $preserveHost = false): self
    {
        $self = clone $this;

        // @phpstan-ignore-next-line
        return $self->setUri($uri, $preserveHost);
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function setUri(UriInterface $uri, bool $preserveHost = false): self
    {
        $this->uri = $uri;
        $this->appUri = null;
        $this->uriInited = true;
        if (!$preserveHost)
        {
            $this->headers = [];
            $this->headerNames = [];
            $this->headersInited = true;
        }

        return $this;
    }

    public function getAppUri(?string $serverName = null): UriInterface
    {
        if (null !== $this->appUri)
        {
            return $this->appUri;
        }
        $uriConfig = Server::getServer($serverName)->getConfig()['appUri'] ?? null;
        if ($uriConfig)
        {
            if (\is_callable($uriConfig))
            {
                return $this->appUri = $uriConfig($this->getUri());
            }
            else
            {
                $uri = $this->getUri();

                if (isset($uriConfig['user']))
                {
                    $userInfo = $uriConfig['user'] ?? '';
                    if (isset($uriConfig['pass']))
                    {
                        $userInfo .= ':' . $uriConfig['pass'];
                    }
                }

                return $this->appUri = Uri::makeUri($uriConfig['host'] ?? $uri->getHost(), $uriConfig['path'] ?? $uri->getPath(), $uriConfig['query'] ?? $uri->getQuery(), $uriConfig['port'] ?? $uri->getPort(), $uriConfig['scheme'] ?? $uri->getScheme(), $uriConfig['fragment'] ?? $uri->getFragment(), $userInfo ?? $uri->getUserInfo());
            }
        }
        else
        {
            return $this->appUri = $this->getUri();
        }
    }
}
