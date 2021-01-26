<?php

declare(strict_types=1);

namespace Imi\Server;

class Protocol
{
    const HTTP = 'http';

    const WEBSOCKET = 'websocket';

    const TCP = 'tcp';

    const UDP = 'udp';

    const LONG_CONNECTION_PROTOCOLS = [
        self::WEBSOCKET,
        self::TCP,
    ];

    private function __construct()
    {
    }
}
