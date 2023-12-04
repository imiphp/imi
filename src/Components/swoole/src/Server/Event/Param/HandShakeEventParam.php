<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;

class HandShakeEventParam extends CommonEvent
{
    public function __construct(
        ?object $__target = null,
        /**
         * swoole 请求对象
         */
        public readonly ?IHttpRequest $request = null,

        /**
         * swoole 响应对象
         */
        public readonly ?IHttpResponse $response = null
    ) {
        parent::__construct('handshake', $__target);
    }
}
