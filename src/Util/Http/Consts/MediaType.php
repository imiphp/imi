<?php

namespace Imi\Util\Http\Consts;

/**
 * 常见的媒体类型.
 */
abstract class MediaType
{
    const ALL = '*/*';

    const APPLICATION_ATOM_XML = 'application/atom+xml';

    const APPLICATION_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    const APPLICATION_JSON = 'application/json';

    const APPLICATION_JSON_UTF8 = 'application/json;charset=utf-8';

    const APPLICATION_OCTET_STREAM = 'application/octet-stream';

    const APPLICATION_PDF = 'application/pdf';

    const APPLICATION_PROBLEM_JSON = 'application/problem+json';

    const APPLICATION_PROBLEM_XML = 'application/problem+xml';

    const APPLICATION_RSS_XML = 'application/rss+xml';

    const APPLICATION_STREAM_JSON = 'application/stream+json';

    const APPLICATION_XHTML_XML = 'application/xhtml+xml';

    const APPLICATION_XML = 'application/xml';

    const IMAGE_JPEG = 'image/jpeg';

    const IMAGE_APNG = 'image/apng';

    const IMAGE_PNG = 'image/png';

    const IMAGE_GIF = 'image/gif';

    const IMAGE_WEBP = 'image/webp';

    const IMAGE_ICON = 'image/x-icon';

    const MULTIPART_FORM_DATA = 'multipart/form-data';

    const TEXT_EVENT_STREAM = 'text/event-stream';

    const TEXT_HTML = 'text/html';

    const TEXT_MARKDOWN = 'text/markdown';

    const TEXT_PLAIN = 'text/plain';

    const TEXT_XML = 'text/xml';

    const GRPC = 'application/grpc';

    const GRPC_PROTO = 'application/grpc+proto';

    const GRPC_JSON = 'application/grpc+json';
}
