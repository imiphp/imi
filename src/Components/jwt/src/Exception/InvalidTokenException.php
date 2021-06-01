<?php

declare(strict_types=1);

namespace Imi\JWT\Exception;

/**
 * Token 验证失败.
 */
class InvalidTokenException extends \Exception
{
    public function __construct(string $message = 'Invalid Token', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
