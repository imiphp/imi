<?php

declare(strict_types=1);

namespace Imi\Fpm\Http\Message;

use Imi\Server\Http\Message\Emitter\Handler\IEmitHandler;

class FpmEmitHandler implements IEmitHandler
{
    public function send(string $data): bool
    {
        echo $data;
        ob_flush();

        return true;
    }
}
