<?php

declare(strict_types=1);

namespace Imi\Grpc;

use Imi\Log\Log;
use Imi\Main\BaseMain;
use Imi\Util\Imi;

class Main extends BaseMain
{
    public function __init(): void
    {
        if (Imi::checkAppType('swoole') && !class_exists(\Swoole\Coroutine\Http2\Client::class, false))
        {
            Log::error('Please compile Swoole with http2: https://wiki.swoole.com/#/environment?id=-enable-http2');
            exit(1);
        }
    }
}
