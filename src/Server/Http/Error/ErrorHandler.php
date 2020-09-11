<?php

namespace Imi\Server\Http\Error;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;

/**
 * @Bean("HttpErrorHandler")
 */
class ErrorHandler implements IErrorHandler
{
    protected $handler = JsonErrorHandler::class;

    public function handle(\Throwable $throwable): bool
    {
        return RequestContext::getServerBean($this->handler)->handle($throwable);
    }
}
