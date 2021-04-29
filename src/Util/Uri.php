<?php

declare(strict_types=1);

namespace Imi\Util;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * 协议，如：http.
     */
    protected string $scheme = '';

    /**
     * 主机名.
     */
    protected string $host = '';

    /**
     * 端口号.
     */
    protected ?int $port = null;

    /**
     * 用户信息
     * 格式：用户名:密码
     */
    protected string $userInfo = '';

    /**
     * 路径.
     */
    protected string $path = '';

    /**
     * 查询参数，在?后的.
     */
    protected string $query = '';

    /**
     * 锚点，在#后的.
     */
    protected string $fragment = '';

    /**
     * 协议标准端口.
     */
    protected static array $schemePorts = [
        'http'  => 80,
        'https' => 443,
        'ftp'   => 21,
    ];

    public function __construct(string $uri = '')
    {
        $uriOption = parse_url($uri);
        if (false === $uriOption)
        {
            throw new \InvalidArgumentException(sprintf('Uri %s parse error', $uri));
        }
        $this->scheme = $uriOption['scheme'] ?? '';
        $this->host = $uriOption['host'] ?? '';
        $this->port = $uriOption['port'] ?? null;
        $userInfo = $uriOption['user'] ?? '';
        if (isset($uriOption['pass']))
        {
            $userInfo .= ':' . $uriOption['pass'];
        }
        $this->userInfo = $userInfo;
        $this->path = $uriOption['path'] ?? '';
        $this->query = $uriOption['query'] ?? '';
        $this->fragment = $uriOption['fragment'] ?? '';
    }

    /**
     * 生成Uri文本.
     *
     * @param int $port
     *
     * @return string
     */
    public static function makeUriString(string $host, string $path, string $query = '', ?int $port = null, string $scheme = 'http', string $fragment = '', string $userInfo = '')
    {
        // 协议
        if ('' !== $scheme)
        {
            $scheme .= '://';
        }
        // 用户信息
        if ('' !== $userInfo)
        {
            $userInfo .= '@';
        }
        // 端口
        if (null !== $port)
        {
            $port = ':' . $port;
        }
        // 路径
        $path = '/' . ltrim($path, '/');
        // 查询参数
        $query = ('' === $query ? '' : ('?' . $query));
        // 锚点
        $fragment = ('' === $fragment ? '' : ('#' . $fragment));

        return "{$scheme}{$userInfo}{$host}{$port}{$path}{$query}{$fragment}";
    }

    /**
     * 生成Uri对象
     *
     * @return static
     */
    public static function makeUri(string $host, string $path, string $query = '', ?int $port = 80, string $scheme = 'http', string $fragment = '', string $userInfo = '')
    {
        return new static(static::makeUriString($host, $path, $query, $port, $scheme, $fragment, $userInfo));
    }

    /**
     * 获取连接到服务器的端口.
     */
    public static function getServerPort(UriInterface $uri): ?int
    {
        $port = $uri->getPort();
        if (!$port)
        {
            $port = static::$schemePorts[$uri->getScheme()] ?? null;
        }

        return $port;
    }

    /**
     * 获取域名
     * 格式：host[:port].
     */
    public static function getDomain(UriInterface $uri): string
    {
        $result = $uri->getHost();
        if (null !== ($port = $uri->getPort()))
        {
            $result .= ':' . $port;
        }

        return $result;
    }

    /**
     * Retrieve the scheme component of the URI.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     *
     * @return string the URI scheme
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     *
     * @return string the URI authority, in "[user-info@]host[:port]" format
     */
    public function getAuthority()
    {
        $result = $this->host;
        if ('' !== $this->userInfo)
        {
            $result = $this->userInfo . '@' . $result;
        }
        if (null !== $this->port)
        {
            $result .= ':' . $this->port;
        }

        return $result;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string the URI user information, in "username[:password]" format
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * @return string the URI host
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return int|null the URI port
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     *
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @return string the URI path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     *
     * @return string the URI query string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @return string the URI fragment
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme the scheme to use with the new instance
     *
     * @return static a new instance with the specified scheme
     *
     * @throws \InvalidArgumentException for invalid or unsupported schemes
     */
    public function withScheme($scheme)
    {
        if (!\is_string($scheme))
        {
            throw new \InvalidArgumentException('Invalid or unsupported schemes');
        }
        $self = clone $this;
        $self->scheme = $scheme;

        return $self;
    }

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string      $user     the user name to use for authority
     * @param string|null $password the password associated with $user
     *
     * @return static a new instance with the specified user information
     */
    public function withUserInfo($user, $password = null)
    {
        $self = clone $this;
        $self->userInfo = $user;
        if (null !== $password)
        {
            $self->userInfo .= ':' . $password;
        }

        return $self;
    }

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host the hostname to use with the new instance
     *
     * @return static a new instance with the specified host
     *
     * @throws \InvalidArgumentException for invalid hostnames
     */
    public function withHost($host)
    {
        $self = clone $this;
        $self->host = $host;

        return $self;
    }

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param int|null $port the port to use with the new instance; a null value
     *                       removes the port information
     *
     * @return static a new instance with the specified port
     *
     * @throws \InvalidArgumentException for invalid ports
     */
    public function withPort($port)
    {
        $self = clone $this;
        $self->port = $port;

        return $self;
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path the path to use with the new instance
     *
     * @return static a new instance with the specified path
     *
     * @throws \InvalidArgumentException for invalid paths
     */
    public function withPath($path)
    {
        $self = clone $this;
        $self->path = $path;

        return $self;
    }

    /**
     * Return an instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     *
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query the query string to use with the new instance
     *
     * @return static a new instance with the specified query string
     *
     * @throws \InvalidArgumentException for invalid query strings
     */
    public function withQuery($query)
    {
        $self = clone $this;
        $self->query = $query;

        return $self;
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment the fragment to use with the new instance
     *
     * @return static a new instance with the specified fragment
     */
    public function withFragment($fragment)
    {
        $self = clone $this;
        $self->fragment = $fragment;

        return $self;
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     */
    public function __toString(): string
    {
        return static::makeUriString($this->host, $this->path, $this->query, $this->port, $this->scheme, $this->fragment, $this->userInfo);
    }
}
