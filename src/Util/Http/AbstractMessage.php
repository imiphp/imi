<?php

declare(strict_types=1);

namespace Imi\Util\Http;

use Imi\Util\Http\Contract\IMessage;
use Psr\Http\Message\StreamInterface;

abstract class AbstractMessage implements IMessage
{
    /**
     * Http协议版本.
     *
     * @var string
     */
    protected string $protocolVersion = '1.1';

    /**
     * 头.
     *
     * @var array
     */
    protected array $headers = [];

    /**
     * 头名称数组
     * 小写的头 => 第一次使用的头名称.
     *
     * @var array
     */
    protected array $headerNames = [];

    /**
     * 消息主体.
     *
     * @var \Psr\Http\Message\StreamInterface
     */
    protected StreamInterface $body;

    /**
     * 协议版本是否初始化.
     *
     * @var bool
     */
    protected bool $protocolVersionInited = false;

    /**
     * headers 是否初始化.
     *
     * @var bool
     */
    protected bool $headersInited = false;

    /**
     * body 是否初始化.
     *
     * @var bool
     */
    protected bool $bodyInited = false;

    /**
     * 初始化协议版本.
     *
     * @return void
     */
    protected function initProtocolVersion()
    {
    }

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version
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
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     *
     * @return static
     */
    public function withProtocolVersion($version)
    {
        $self = clone $this;
        $self->protocolVersion = $version;
        $self->protocolVersionInited = true;

        return $self;
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * @param string $version HTTP protocol version
     *
     * @return static
     */
    public function setProtocolVersion(string $version): self
    {
        $this->protocolVersion = $version;
        $this->protocolVersionInited = true;

        return $this;
    }

    /**
     * 初始化 headers.
     *
     * @return void
     */
    protected function initHeaders()
    {
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return array Returns an associative array of the message's headers. Each
     *               key MUST be a header name, and each value MUST be an array of strings
     *               for that header.
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
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name case-insensitive header field name
     *
     * @return bool Returns true if any header names match the given header
     *              name using a case-insensitive string comparison. Returns false if
     *              no matching header name is found in the message.
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
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name case-insensitive header field name
     *
     * @return string[] An array of string values as provided for the given
     *                  header. If the header does not appear in the message, this method MUST
     *                  return an empty array.
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
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name case-insensitive header field name
     *
     * @return string A string of values as provided for the given header
     *                concatenated together using a comma. If the header does not appear in
     *                the message, this method MUST return an empty string.
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
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string          $name  case-insensitive header field name
     * @param string|string[] $value header value(s)
     *
     * @return static
     *
     * @throws \InvalidArgumentException for invalid header names or values
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
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * @param string          $name  case-insensitive header field name
     * @param string|string[] $value header value(s)
     *
     * @return static
     *
     * @throws \InvalidArgumentException for invalid header names or values
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
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string          $name  case-insensitive header field name to add
     * @param string|string[] $value header value(s)
     *
     * @return static
     *
     * @throws \InvalidArgumentException for invalid header names or values
     */
    public function withAddedHeader($name, $value)
    {
        $self = clone $this;
        if (!$self->headersInited)
        {
            $self->initHeaders();
            $self->headersInited = true;
        }

        return $self->addHeader($name, $value);
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * @param string          $name  case-insensitive header field name to add
     * @param string|string[] $value header value(s)
     *
     * @return static
     *
     * @throws \InvalidArgumentException for invalid header names or values
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
            throw new \InvalidArgumentException('invalid header names or values');
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
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name case-insensitive header field name to remove
     *
     * @return static
     */
    public function withoutHeader($name)
    {
        $self = clone $this;
        if (!$self->headersInited)
        {
            $self->initHeaders();
            $self->headersInited = true;
        }

        return $self->removeHeader($name);
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * @param string $name case-insensitive header field name to remove
     *
     * @return static
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
     * 初始化 body.
     *
     * @return void
     */
    protected function initBody()
    {
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface returns the body as a stream
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
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body body
     *
     * @return static
     *
     * @throws \InvalidArgumentException when the body is not valid
     */
    public function withBody(StreamInterface $body)
    {
        $self = clone $this;
        $self->body = $body;
        $self->bodyInited = true;

        return $self;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * @param StreamInterface $body body
     *
     * @return static
     *
     * @throws \InvalidArgumentException when the body is not valid
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
     * @param array $headers
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
                throw new \InvalidArgumentException('invalid header names or values');
            }
        }

        return $object;
    }
}
