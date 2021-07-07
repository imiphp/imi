<?php

declare(strict_types=1);

namespace Imi\Util\Http\Consts;

/**
 * 常见的媒体类型.
 */
class MediaType
{
    public const ALL = '*/*';

    public const APPLICATION_ATOM_XML = 'application/atom+xml';

    public const APPLICATION_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    public const APPLICATION_JSON = 'application/json';

    public const APPLICATION_JSON_UTF8 = 'application/json;charset=utf-8';

    public const APPLICATION_OCTET_STREAM = 'application/octet-stream';

    public const APPLICATION_PDF = 'application/pdf';

    public const APPLICATION_PROBLEM_JSON = 'application/problem+json';

    public const APPLICATION_PROBLEM_XML = 'application/problem+xml';

    public const APPLICATION_RSS_XML = 'application/rss+xml';

    public const APPLICATION_STREAM_JSON = 'application/stream+json';

    public const APPLICATION_XHTML_XML = 'application/xhtml+xml';

    public const APPLICATION_XML = 'application/xml';

    public const IMAGE_JPEG = 'image/jpeg';

    public const IMAGE_APNG = 'image/apng';

    public const IMAGE_PNG = 'image/png';

    public const IMAGE_GIF = 'image/gif';

    public const IMAGE_WEBP = 'image/webp';

    public const IMAGE_ICON = 'image/x-icon';

    public const MULTIPART_FORM_DATA = 'multipart/form-data';

    public const TEXT_EVENT_STREAM = 'text/event-stream';

    public const TEXT_HTML = 'text/html';

    public const TEXT_MARKDOWN = 'text/markdown';

    public const TEXT_PLAIN = 'text/plain';

    public const TEXT_XML = 'text/xml';

    public const GRPC = 'application/grpc';

    public const GRPC_PROTO = 'application/grpc+proto';

    public const GRPC_JSON = 'application/grpc+json';

    private function __construct()
    {
    }
}
