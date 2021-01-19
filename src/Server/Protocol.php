<?php

declare(strict_types=1);

namespace Imi\Server;

class Protocol
{
    const HTTP = 'http';

    const WEBSOCKET = 'websocket';

    const TCP = 'tcp';

    const UDP = 'udp';

    private function __construct()
    {
    }
}
