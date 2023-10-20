<?php

declare(strict_types=1);

namespace Imi\Swoole\Http\Message;

use Imi\Server\Http\Message\Emitter\Handler\IEmitHandler;
use Swoole\Http\Response;

class SwooleEmitHandler implements IEmitHandler
{
    public function __construct(private readonly Response $response)
    {
    }

    public function send(string $data): bool
    {
        return (bool) $this->response->write($data);
    }
}
