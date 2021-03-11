<?php

namespace Imi\Server\Http\Error;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;

/**
 * @Bean("HttpErrorHandler")
 */
class ErrorHandler implements IErrorHandler
{
    /**
     * @var string
     */
    protected $handler = JsonErrorHandler::class;

    /**
     * @param \Throwable $throwable
     *
     * @return bool
     */
    public function handle(\Throwable $throwable): bool
    {
        return RequestContext::getServerBean($this->handler)->handle($throwable);
    }
}
