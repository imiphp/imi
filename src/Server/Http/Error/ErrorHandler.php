<?php

declare(strict_types=1);

namespace Imi\Server\Http\Error;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;

/**
 * @Bean("HttpErrorHandler")
 */
class ErrorHandler implements IErrorHandler
{
    protected string $handler = JsonErrorHandler::class;

    /**
     * {@inheritDoc}
     */
    public function handle(\Throwable $throwable): bool
    {
        return RequestContext::getServerBean($this->handler)->handle($throwable);
    }
}
