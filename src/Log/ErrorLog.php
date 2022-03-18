<?php

declare(strict_types=1);

namespace Imi\Log;

use Imi\Bean\Annotation\Bean;
use Imi\Util\Traits\TBeanRealClass;

/**
 * @Bean("ErrorLog")
 */
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
     * 注册错误监听.
     */
    public function register(): void
    {
        error_reporting($this->level);
        register_shutdown_function([$this, 'onShutdown']);
        // @phpstan-ignore-next-line
        set_error_handler([$this, 'onError'], $this->catchLevel);
        set_exception_handler([$this, 'onException']);
    }

    /**
     * 错误.
     */
    public function onError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        if ($this->exceptionLevel & $errno)
        {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
        switch ($errno)
        {
            case \E_ERROR:
            case \E_PARSE:
            case \E_CORE_ERROR:
            case \E_COMPILE_ERROR:
            case \E_USER_ERROR:
            case \E_RECOVERABLE_ERROR:
                $method = 'error';
                break;
            case \E_WARNING:
            case \E_CORE_WARNING:
            case \E_COMPILE_WARNING:
            case \E_USER_WARNING:
                $method = 'warning';
                break;
            case \E_NOTICE:
            case \E_USER_NOTICE:
                $method = 'notice';
                break;
            case \E_STRICT:
            case \E_DEPRECATED:
            case \E_USER_DEPRECATED:
            default:
                $method = 'info';
                break;
        }
        Log::$method($errstr);
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

    /**
     * 致命错误.
     */
    public function onException(\Throwable $th): void
    {
        // 支持记录无限级上级日志
        $throwables = [$th];
        $prev = $th;
        do
        {
            $prev = $prev->getPrevious();
            if ($prev)
            {
                $throwables[] = $prev;
            }
        }
        while ($prev);
        $throwables = array_reverse($throwables);
        foreach ($throwables as $throwable)
        {
            // 日志处理
            // @phpstan-ignore-next-line
            Log::error($throwable);
        }
    }
}
