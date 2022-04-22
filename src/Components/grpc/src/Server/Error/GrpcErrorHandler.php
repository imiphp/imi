<?php

declare(strict_types=1);

namespace Imi\Grpc\Server\Error;

use Imi\RequestContext;
use Imi\Server\Http\Error\IErrorHandler;

class GrpcErrorHandler implements IErrorHandler
{
    protected string $handler = DefaultGrpcErrorHandler::class;

    public function handle(\Throwable $throwable): bool
    {
        return RequestContext::getServerBean($this->handler)->handle($throwable);
    }
}
