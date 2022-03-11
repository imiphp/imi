<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Error;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;

/**
 * @Bean("WebSocketErrorHandler")
 */
class ErrorHandler implements IErrorHandler
{
    protected ?string $handler = null;

    /**
     * {@inheritDoc}
     */
    public function handle(\Throwable $throwable): bool
    {
        if ($this->handler)
        {
            return RequestContext::getServerBean($this->handler)->handle($throwable);
        }
        else
        {
            return false;
        }
    }
}
