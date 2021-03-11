<?php

namespace Imi\Log;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Util\Traits\TBeanRealClass;

/**
 * @Bean("ErrorLog")
 */
class ErrorLog
{
    use TBeanRealClass;

    /**
     * 错误级别.
     *
     * @var int
     */
    protected $level = 0;

    /**
     * 注册错误监听.
     *
     * @return void
     */
    public function register()
    {
        error_reporting($this->level);
        register_shutdown_function([$this, 'onShutdown']);
        // @phpstan-ignore-next-line
        set_error_handler([$this, 'onError']);
    }

    /**
     * 错误.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     *
     * @return void
     */
    public function onError($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() & $errno)
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
        Log::$method($errstr, [
            'trace'     => $this->getTrace(),
            'errorFile' => $errfile,
            'errorLine' => $errline,
        ]);
    }

    /**
     * 致命错误.
     *
     * @return void
     */
    public function onShutdown()
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
                Log::error($e['message'], [
                    'trace'      => [],
                    'errorFile'  => $e['file'],
                    'errorLine'  => $e['line'],
                ]);
            }
            $logger = App::getBean('Logger');
            $logger->save();
        }
        catch (\Throwable $th)
        {
            echo $th->getMessage(), ' ', $th->getFile(), ':', $th->getLine(), \PHP_EOL;
        }
    }

    /**
     * 致命错误.
     *
     * @return void
     */
    public function onException(\Throwable $th)
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
        } while ($prev);
        $throwables = array_reverse($throwables);
        foreach ($throwables as $throwable)
        {
            // 日志处理
            Log::error($throwable->getMessage(), [
                'trace'     => $throwable->getTrace(),
                'errorFile' => $throwable->getFile(),
                'errorLine' => $throwable->getLine(),
            ]);
        }
    }

    /**
     * 获取代码调用跟踪.
     *
     * @return array
     */
    protected function getTrace()
    {
        $backtrace = debug_backtrace();
        $index = null;
        $realClassName = static::__getRealClassName();
        foreach ($backtrace as $i => $item)
        {
            if ($realClassName === $item['class'])
            {
                $index = $i + 2;
                break;
            }
        }
        if (null === $index)
        {
            return [];
        }

        return array_splice($backtrace, $index);
    }
}
