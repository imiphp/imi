<?php

declare(strict_types=1);

namespace Imi\Swoole\Http\Message;

use Imi\Server\Http\Message\Emitter\Handler\IEmitHandler;
use Swoole\Http\Response;

class SwooleEmitHandler implements IEmitHandler
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function send(string $data): bool
    {
        return (bool) $this->response->write($data);
    }
}
