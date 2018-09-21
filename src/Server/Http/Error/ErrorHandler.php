<?php
namespace Imi\Server\Http\Error;

use Imi\App;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("HttpErrorHandler")
 */
class ErrorHandler implements IErrorHandler
{
    protected $handler = JsonErrorHandler::class;

    public function handle(\Throwable $throwable): bool
    {
        return App::getBean($this->handler)->handle($throwable);
    }
}