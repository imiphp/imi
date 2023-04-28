<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message\Emitter\Handler;

interface IEmitHandler
{
    public function send(string $data): void;
}
