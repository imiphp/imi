<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Psr\Http\Message\ServerRequestInterface;

class OpenEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 服务器对象
         */
        public readonly ?ISwooleServer $server = null,

        /**
         * 请求对象.
         */
        public readonly ?ServerRequestInterface $request = null
    ) {
        parent::__construct('open', $server);
    }
}
