<?php

declare(strict_types=1);

namespace Imi\Workerman\Http\Message;

use Imi\Server\Http\Message\Emitter\Handler\IEmitHandler;
use Workerman\Connection\TcpConnection;

class WorkermanEmitHandler implements IEmitHandler
{
    public function __construct(private readonly TcpConnection $connection)
    {
    }

    public function send(string $data): bool
    {
        return (bool) $this->connection->send($data, true);
    }
}
