<?php

declare(strict_types=1);

namespace Imi\Server\Grpc\Error;

use Imi\RequestContext;
use Imi\Server\Http\Error\IErrorHandler;

class GrpcErrorHandler implements IErrorHandler
{
    /**
     * @var string
     */
    protected $handler = DefaultGrpcErrorHandler::class;

    public function handle(\Throwable $throwable): bool
    {
        return RequestContext::getServerBean($this->handler)->handle($throwable);
    }
}
