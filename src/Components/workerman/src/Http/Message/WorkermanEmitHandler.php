<?php

declare(strict_types=1);

namespace Imi\Workerman\Http\Message;

use Imi\Server\Http\Message\Emitter\Handler\IEmitHandler;
use Workerman\Connection\TcpConnection;

class WorkermanEmitHandler implements IEmitHandler
{
    private TcpConnection $connection;

    public function __construct(TcpConnection $connection)
    {
        $this->connection = $connection;
    }

    public function send(string $data): bool
    {
        return (bool) $this->connection->send($data, true);
    }
}
