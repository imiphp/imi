<?php

declare(strict_types=1);

namespace Imi\Util\Http;

use Imi\Util\Http\Contract\IMessage;
use Psr\Http\Message\StreamInterface;

abstract class AbstractMessage implements IMessage
{
    /**
     * Http协议版本.
     */
    protected string $protocolVersion = '1.1';

    /**
     * 头.
     */
    protected array $headers = [];

    /**
     * 头名称数组
     * 小写的头 => 第一次使用的头名称.
     */
    protected array $headerNames = [];

    /**
     * 消息主体.
     */
    protected StreamInterface $body;

    /**
     * 协议版本是否初始化.
     */
    protected bool $protocolVersionInited = false;

    /**
     * headers 是否初始化.
     */
    protected bool $headersInited = false;

    /**
     * body 是否初始化.
     */
    protected bool $bodyInited = false;

    /**
     * 初始化协议版本.
     */
    protected function initProtocolVersion(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getProtocolVersion()
    {
        if (!$this->protocolVersionInited)
        {
            $this->initProtocolVersion();
            $this->protocolVersionInited = true;
        }

        return $this->protocolVersion;
    }

    /**
     * {@inheritDoc}
     */
    public function withProtocolVersion($version)
    {
        $self = clone $this;
        $self->protocolVersion = $version;
        $self->protocolVersionInited = true;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function setProtocolVersion(string $version): self
    {
        $this->protocolVersion = $version;
        $this->protocolVersionInited = true;

        return $this;
    }

    /**
     * 初始化 headers.
     */
    protected function initHeaders(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        if (!$this->headersInited)
        {
            $this->initHeaders();
            $this->headersInited = true;
        }

        return $this->headers;
    }

    /**
     * {@inheritDoc}
     */
    public function hasHeader($name)
    {
        if (!$this->headersInited)
        {
            $this->initHeaders();
            $this->headersInited = true;
        }
        $lowerName = strtolower($name);
        $headerNames = $this->headerNames;
        if (isset($headerNames[$lowerName]))
        {
            $name = $headerNames[$lowerName];
        }

        return isset($this->headers[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeader($name)
    {
        if (!$this->headersInited)
        {
            $this->initHeaders();
            $this->headersInited = true;
        }
        $lowerName = strtolower($name);
        $headerNames = $this->headerNames;
        if (isset($headerNames[$lowerName]))
        {
            $name = $headerNames[$lowerName];
        }
        $headers = $this->headers;
        if (isset($headers[$name]))
        {
            return $headers[$name];
        }
        else
        {
            return [];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaderLine($name)
    {
        if (!$this->headersInited)
        {
            $this->initHeaders();
            $this->headersInited = true;
        }
        $lowerName = strtolower($name);
        $headerNames = $this->headerNames;
        if (isset($headerNames[$lowerName]))
        {
            $name = $headerNames[$lowerName];
        }
        $headers = $this->headers;
        if (!isset($headers[$name]))
        {
            return '';
        }

        return implode(',', $headers[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function withHeader($name, $value)
    {
        $self = clone $this;
        if (!$self->headersInited)
        {
            $self->initHeaders();
            $self->headersInited = true;
        }

        return $this->mergeHeaders([$name => $value], $self);
    }

    /**
     * {@inheritDoc}
     */
    public function setHeader(string $name, $value): self
    {
        if (!$this->headersInited)
        {
            $this->initHeaders();
            $this->headersInited = true;
        }

        return $this->mergeHeaders([$name => $value]);
    }

    /**
     * {@inheritDoc}
     */
    public function withAddedHeader($name, $value)
    {
        $self = clone $this;
        if (!$self->headersInited)
        {
            $self->initHeaders();
            $self->headersInited = true;
        }

        // @phpstan-ignore-next-line
        return $self->addHeader($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function addHeader(string $name, $value): self
    {
        if (!$this->headersInited)
        {
            $this->initHeaders();
            $this->headersInited = true;
        }
        $lowerName = strtolower($name);
        $headerNames = &$this->headerNames;
        if (isset($headerNames[$lowerName]))
        {
            $name = $headerNames[$lowerName];
        }
        else
        {
            $headerNames[$lowerName] = $name;
        }

        if (\is_string($value))
        {
            $value = [$value];
        }
        elseif (!\is_array($value))
        {
            throw new \InvalidArgumentException('Invalid header names or values');
        }

        $headers = &$this->headers;
        if (isset($headers[$name]))
        {
            $headers[$name] = array_merge($headers[$name], $value);
        }
        else
        {
            $headers[$name] = $value;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withoutHeader($name)
    {
        $self = clone $this;
        if (!$self->headersInited)
        {
            $self->initHeaders();
            $self->headersInited = true;
        }

        // @phpstan-ignore-next-line
        return $self->removeHeader($name);
    }

    /**
     * {@inheritDoc}
     */
    public function removeHeader(string $name): self
    {
        if (!$this->headersInited)
        {
            $this->initHeaders();
            $this->headersInited = true;
        }
        $lowerName = strtolower($name);
        if (isset($this->headerNames[$lowerName]))
        {
            $name = $this->headerNames[$lowerName];
        }
        if (isset($this->headers[$name]))
        {
            unset($this->headers[$name]);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function initBody(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getBody()
    {
        if (!$this->bodyInited)
        {
            $this->initBody();
            $this->bodyInited = true;
        }

        return $this->body;
    }

    /**
     * {@inheritDoc}
     */
    public function withBody(StreamInterface $body)
    {
        $self = clone $this;
        $self->body = $body;
        $self->bodyInited = true;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function setBody(StreamInterface $body): self
    {
        $this->body = $body;
        $this->bodyInited = true;

        return $this;
    }

    /**
     * 在当前实例下设置头.
     *
     * @param static|null $object
     *
     * @return static
     */
    protected function mergeHeaders(array $headers, self $object = null): self
    {
        if (null === $object)
        {
            $object = $this;
        }
        $headerNames = &$object->headerNames;
        $objectHeaders = &$object->headers;
        foreach ($headers as $name => $value)
        {
            $lowerName = strtolower($name);
            if (isset($headerNames[$lowerName]))
            {
                $name = $headerNames[$lowerName];
            }
            else
            {
                $headerNames[$lowerName] = $name;
            }
            if (\is_string($value))
            {
                $objectHeaders[$name] = [$value];
            }
            elseif (\is_array($value))
            {
                $objectHeaders[$name] = $value;
            }
            else
            {
                throw new \InvalidArgumentException('Invalid header names or values');
            }
        }

        return $object;
    }
}
