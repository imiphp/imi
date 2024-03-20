<?php

declare(strict_types=1);

namespace Imi\Log;

use Imi\Bean\Annotation\Bean;
use Imi\Util\Traits\TBeanRealClass;

#[Bean(name: 'ErrorLog')]
class ErrorLog
{
    use TBeanRealClass;

    /**
     * PHP 报告的错误级别.
     */
    protected int $level = \E_ALL;

    /**
     * 错误捕获级别.
     */
    protected int $catchLevel = \E_ALL | \E_STRICT;

    /**
     * 抛出异常的错误级别.
     */
    protected int $exceptionLevel = \E_ERROR | \E_PARSE | \E_CORE_ERROR | \E_COMPILE_ERROR | \E_USER_ERROR | \E_RECOVERABLE_ERROR | \E_WARNING | \E_CORE_WARNING | \E_COMPILE_WARNING | \E_USER_WARNING;

    /**
     * 异常事件处理器.
     *
     * @var array<class-string<IErrorEventHandler>>
     */
    protected array $errorEventHandlers = [];

    /**
     * 注册错误监听.
     */
    public function register(): void
    {
        error_reporting($this->level);
        register_shutdown_function([$this, 'onShutdown']);
        // @phpstan-ignore-next-line
        set_error_handler($this->onError(...), $this->catchLevel);
        set_exception_handler($this->onException(...));
    }

    /**
     * 错误.
     */
    public function onError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        if (0 === (error_reporting() & $errno))
        {
            return;
        }
        foreach ($this->errorEventHandlers as $class)
        {
            $handler = new $class();
            $handler->handleError($errno, $errstr, $errfile, $errline);
            if ($handler->isPropagationStopped())
            {
                return;
            }
        }
        if ($this->exceptionLevel & $errno)
        {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
        $level = match ($errno)
        {
            \E_ERROR, \E_PARSE, \E_CORE_ERROR, \E_COMPILE_ERROR, \E_USER_ERROR, \E_RECOVERABLE_ERROR => \Psr\Log\LogLevel::ERROR,
            \E_WARNING, \E_CORE_WARNING, \E_COMPILE_WARNING, \E_USER_WARNING => \Psr\Log\LogLevel::WARNING,
            \E_NOTICE, \E_USER_NOTICE => \Psr\Log\LogLevel::NOTICE,
            default => \Psr\Log\LogLevel::INFO,
        };
        Log::log($level, $errstr);
    }

    public function onException(\Throwable $throwable): void
    {
        foreach ($this->errorEventHandlers as $class)
        {
            $handler = new $class();
            $handler->handleException($throwable);
            if ($handler->isPropagationStopped())
            {
                return;
            }
        }
        Log::error($throwable);
    }

    /**
     * 致命错误.
     */
    public function onShutdown(): void
    {
        try
        {
            $e = error_get_last();
            if ($e && \in_array($e['type'], [
                \E_ERROR,
                \E_PARSE,
                \E_CORE_ERROR,
                \E_COMPILE_ERROR,
                \E_USER_ERROR,
                \E_RECOVERABLE_ERROR,
            ]))
            {
                Log::error($e['message']);
            }
        }
        catch (\Throwable $th)
        {
            echo $th->getMessage(), ' ', $th->getFile(), ':', $th->getLine(), \PHP_EOL;
        }
    }
}
