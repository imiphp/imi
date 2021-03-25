<?php

declare(strict_types=1);

namespace Imi\Log;

use Imi\App;

class Log
{
    private function __construct()
    {
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     */
    public static function log($level, $message, array $context = []): void
    {
        App::getBean('Logger')->log($level, $message, static::parseContext($context));
    }

    /**
     * System is unusable.
     *
     * @param string $message
     */
    public static function emergency($message, array $context = []): void
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
     */
    public static function alert($message, array $context = []): void
    {
        App::getBean('Logger')->alert($message, static::parseContext($context));
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     */
    public static function critical($message, array $context = []): void
    {
        App::getBean('Logger')->critical($message, static::parseContext($context));
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     */
    public static function error($message, array $context = []): void
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
     */
    public static function warning($message, array $context = []): void
    {
        App::getBean('Logger')->warning($message, static::parseContext($context));
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     */
    public static function notice($message, array $context = []): void
    {
        App::getBean('Logger')->notice($message, static::parseContext($context));
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     */
    public static function info($message, array $context = []): void
    {
        App::getBean('Logger')->info($message, static::parseContext($context));
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     */
    public static function debug($message, array $context = []): void
    {
        App::getBean('Logger')->debug($message, static::parseContext($context));
    }

    /**
     * 获取代码调用跟踪.
     */
    protected static function getTrace(?array &$topTraces): array
    {
        $limit = App::getBean('ErrorLog')->getBacktraceLimit();
        $backtrace = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit);
        $result = array_splice($backtrace, 3);
        $topTraces = $backtrace;

        return $result;
    }

    /**
     * 获取错误文件位置.
     */
    protected static function getErrorFile(?array $topThreeTraces = null): array
    {
        $backtrace = $topThreeTraces ?? debug_backtrace(0, 3);
        $secondItem = $backtrace[2] ?? null;

        return [$secondItem['file'] ?? '', $secondItem['line'] ?? 0];
    }

    /**
     * 处理context.
     */
    protected static function parseContext(array $context): array
    {
        $topThreeTraces = null;
        $context['trace'] ??= static::getTrace($topThreeTraces);
        if (!isset($context['errorFile']))
        {
            list($file, $line) = static::getErrorFile($topThreeTraces);
            $context['errorFile'] = $file;
            $context['errorLine'] = $line;
        }

        return $context;
    }
}
