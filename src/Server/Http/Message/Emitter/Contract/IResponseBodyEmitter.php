<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message\Emitter\Contract;

use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\Http\Message\Emitter\Handler\IEmitHandler;

interface IResponseBodyEmitter
{
    public function init(IHttpResponse &$response, IEmitHandler $handler): void;

    public function getHandler(): IEmitHandler;

    public function send(): void;
}
