<?php
namespace Imi\Log;

use Imi\App;
use Imi\Config;
use Imi\Worker;
use Imi\Util\File;


abstract class Log
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function log($level, $message, array $context = array())
    {
        App::getBean('Logger')->log($level, $message, static::parseContext($context));
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function emergency($message, array $context = array())
    {
        App::getBean('Logger')->emergency($message, static::parseContext($context));
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function alert($message, array $context = array())
    {
        App::getBean('Logger')->alert($message, static::parseContext($context));
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function critical($message, array $context = array())
    {
        App::getBean('Logger')->critical($message, static::parseContext($context));
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function error($message, array $context = array())
    {
        App::getBean('Logger')->error($message, static::parseContext($context));
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function warning($message, array $context = array())
    {
        App::getBean('Logger')->warning($message, static::parseContext($context));
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function notice($message, array $context = array())
    {
        App::getBean('Logger')->notice($message, static::parseContext($context));
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function info($message, array $context = array())
    {
        App::getBean('Logger')->info($message, static::parseContext($context));
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function debug($message, array $context = array())
    {
        App::getBean('Logger')->debug($message, static::parseContext($context));
    }
    
    /**
     * 获取代码调用跟踪
     * @return array
     */
    private static function getTrace()
    {
        $backtrace = debug_backtrace();
        return array_splice($backtrace, 3);
    }

    /**
     * 获取错误文件位置
     *
     * @return array
     */
    private static function getErrorFile()
    {
        $backtrace = debug_backtrace(0, 3);
        return [$backtrace[2]['file'] ?? '', $backtrace[2]['line'] ?? 0];
    }
    
    /**
     * 处理context
     *
     * @param array $context
     * @return array
     */
    private static function parseContext($context)
    {
        if(!isset($context['trace']))
        {
            $context['trace'] = static::getTrace();
        }
        if(!isset($context['errorFile']))
        {
            list($file, $line) = static::getErrorFile();
            $context['errorFile'] = $file;
            $context['errorLine'] = $line;
        }
        return $context;
    }
}