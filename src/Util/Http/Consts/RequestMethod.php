<?php

declare(strict_types=1);

namespace Imi\Util\Http\Consts;

/**
 * 常见的http请求方法.
 */
class RequestMethod
{
    public const GET = 'GET';

    public const POST = 'POST';

    public const HEAD = 'HEAD';

    public const PUT = 'PUT';

    public const PATCH = 'PATCH';

    public const DELETE = 'DELETE';

    public const OPTIONS = 'OPTIONS';

    public const TRACE = 'TRACE';

    private function __construct()
    {
    }
}
