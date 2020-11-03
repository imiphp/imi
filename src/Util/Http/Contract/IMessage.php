<?php

namespace Imi\Util\Http\Contract;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

interface IMessage extends MessageInterface
{
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
    public function setProtocolVersion(string $version): self;

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
    public function setHeader(string $name, $value): self;

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
    public function addHeader(string $name, $value): self;

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * @param string $name case-insensitive header field name to remove
     *
     * @return static
     */
    public function removeHeader(string $name): self;

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
    public function setBody(StreamInterface $body): self;
}
