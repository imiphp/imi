<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message\Contract;

use Imi\Util\Http\Contract\IServerRequest;
use Imi\Util\Socket\IPEndPoint;

/**
 * Http 请求接口.
 */
interface IHttpRequest extends IServerRequest
{
    /**
     * 获取客户端地址
     */
    public function getClientAddress(): IPEndPoint;
}
