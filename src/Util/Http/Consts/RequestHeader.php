<?php

declare(strict_types=1);

namespace Imi\Util\Http\Consts;

/**
 * 常见的http请求头.
 */
class RequestHeader
{
    public const ACCEPT = 'Accept';
    public const ACCEPT_CHARSET = 'Accept-Charset';
    public const ACCEPT_ENCODING = 'Accept-Encoding';
    public const ACCEPT_LANGUAGE = 'Accept-Language';
    public const ACCEPT_DATETIME = 'Accept-Datetime';
    public const AUTHORIZATION = 'Authorization';
    public const CACHE_CONTROL = 'Cache-Control';
    public const CONNECTION = 'Connection';
    public const COOKIE = 'Cookie';
    public const CONTENT_LENGTH = 'Content-Length';
    public const CONTENT_MD5 = 'Content-MD5';
    public const CONTENT_TYPE = 'Content-Type';
    public const DATE = 'Date';
    public const EXPECT = 'Expect';
    public const FROM = 'From';
    public const HOST = 'Host';
    public const IF_MATCH = 'If-Match';
    public const IF_MODIFIED_SINCE = 'If-Modified-Since';
    public const IF_NONE_MATCH = 'If-None-Match';
    public const IF_RANGE = 'If-Range';
    public const IF_UNMODIFIED_SINCE = 'If-Unmodified-Since';
    public const MAX_FORWARDS = 'Max-Forwards';
    public const ORIGIN = 'Origin';
    public const PRAGMA = 'Pragma';
    public const PROXY_AUTHORIZATION = 'Proxy-Authorization';
    public const RANGE = 'Range';
    public const REFERER = 'Referer';
    public const TE = 'TE';
    public const USER_AGENT = 'User-Agent';
    public const UPGRADE = 'Upgrade';
    public const VIA = 'Via';
    public const WARNING = 'Warning';
    public const TRAILER = 'trailer';

    private function __construct()
    {
    }
}
