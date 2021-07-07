<?php

declare(strict_types=1);

namespace Imi\Util\Http\Consts;

/**
 * 常见的http响应头.
 */
class ResponseHeader
{
    public const ACCESS_CONTROL_ALLOW_ORIGIN = 'Access-Control-Allow-Origin';
    public const ACCEPT_PATCH = 'Accept-Patch';
    public const ACCEPT_RANGES = 'Accept-Ranges';
    public const AGE = 'Age';
    public const ALLOW = 'Allow';
    public const CACHE_CONTROL = 'Cache-Control';
    public const CONNECTION = 'Connection';
    public const CONTENT_DISPOSITION = 'Content-Disposition';
    public const CONTENT_ENCODING = 'Content-Encoding';
    public const CONTENT_LANGUAGE = 'Content-Language';
    public const CONTENT_LENGTH = 'Content-Length';
    public const CONTENT_LOCATION = 'Content-Location';
    public const CONTENT_MD5 = 'Content-MD5';
    public const CONTENT_RANGE = 'Content-Range';
    public const CONTENT_TYPE = 'Content-Type';
    public const DATE = 'Date';
    public const ETAG = 'ETag';
    public const EXPIRES = 'Expires';
    public const LAST_MODIFIED = 'Last-Modified';
    public const LINK = 'Link';
    public const LOCATION = 'Location';
    public const P3P = 'P3P';
    public const PRAGMA = 'Pragma';
    public const PROXY_AUTHENTICATE = 'Proxy-Authenticate';
    public const PUBLIC_KEY_PINS = 'Public-Key-Pins';
    public const REFRESH = 'Refresh';
    public const RETRY_AFTER = 'Retry-After';
    public const SERVER = 'Server';
    public const SET_COOKIE = 'Set-Cookie';
    public const STATUS = 'Status';
    public const TRAILER = 'Trailer';
    public const TRANSFER_ENCODING = 'Transfer-Encoding';
    public const UPGRADE = 'Upgrade';
    public const VARY = 'Vary';
    public const VIA = 'Via';
    public const WARNING = 'Warning';
    public const WWW_AUTHENTICATE = 'WWW-Authenticate';

    private function __construct()
    {
    }
}
