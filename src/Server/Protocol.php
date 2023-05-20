<?php

declare(strict_types=1);

namespace Imi\Server;

class Protocol
{
    use \Imi\Util\Traits\TStaticClass;

    public const HTTP = 'http';

    public const WEBSOCKET = 'websocket';

    public const TCP = 'tcp';

    public const UDP = 'udp';

    public const LONG_CONNECTION_PROTOCOLS = [
        self::WEBSOCKET,
        self::TCP,
    ];
}
