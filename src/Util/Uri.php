<?php

declare(strict_types=1);

namespace Imi\Util;

use function parse_url;
use Psr\Http\Message\UriInterface;
use function sprintf;
use function str_replace;

class Uri implements UriInterface, \Stringable
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
        $isUnixSocket = str_starts_with($uri, 'unix:///');
        if ($isUnixSocket)
        {
            $uri = str_replace('unix:///', 'unix://', $uri);
        }
        $uriOption = parse_url($uri);
        if (false === $uriOption)
        {
            throw new \InvalidArgumentException(sprintf('Uri %s parse error', $uri));
        }
        if ($isUnixSocket)
        {
            $uriOption['host'] = "/{$uriOption['host']}{$uriOption['path']}";
            $uriOption['path'] = '';
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
        if ('' !== $path)
        {
            $path = '/' . ltrim($path, '/');
        }
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
     * {@inheritDoc}
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritDoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritDoc}
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function withHost($host)
    {
        $self = clone $this;
        $self->host = $host;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function withPort($port)
    {
        $self = clone $this;
        $self->port = $port;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function withPath($path)
    {
        $self = clone $this;
        $self->path = $path;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function withQuery($query)
    {
        $self = clone $this;
        $self->query = $query;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function withFragment($fragment)
    {
        $self = clone $this;
        $self->fragment = $fragment;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return static::makeUriString($this->host, $this->path, $this->query, $this->port, $this->scheme, $this->fragment, $this->userInfo);
    }
}
