<?php

declare(strict_types=1);

namespace Imi\Async\Exception;

/**
 * 异步超时异常.
 */
class AsyncTimeoutException extends \Exception
{
    public function __construct(string $message = 'Async timeout', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
