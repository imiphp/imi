<?php

declare(strict_types=1);

namespace Imi\Log;

use Imi\App;

class Log
{
    use \Imi\Util\Traits\TStaticClass;

    public static function get(?string $channelName = null): MonoLogger
    {
        // @phpstan-ignore-next-line
        return App::getBean('Logger')->getLogger($channelName);
    }

    /**
     * Logs with an arbitrary level.
     */
    public static function log(mixed $level, string|\Stringable|\Throwable $message, array $context = [], ?string $channelName = null): void
    {
        self::get($channelName)->log($level, $message, $context);
    }

    /**
     * System is unusable.
     */
    public static function emergency(string|\Stringable|\Throwable $message, array $context = [], ?string $channelName = null): void
    {
        self::get($channelName)->emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     */
    public static function alert(string|\Stringable|\Throwable $message, array $context = [], ?string $channelName = null): void
    {
        self::get($channelName)->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     */
    public static function critical(string|\Stringable|\Throwable $message, array $context = [], ?string $channelName = null): void
    {
        self::get($channelName)->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     */
    public static function error(string|\Stringable|\Throwable $message, array $context = [], ?string $channelName = null): void
    {
        self::get($channelName)->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     */
    public static function warning(string|\Stringable|\Throwable $message, array $context = [], ?string $channelName = null): void
    {
        self::get($channelName)->warning($message, $context);
    }

    /**
     * Normal but significant events.
     */
    public static function notice(string|\Stringable|\Throwable $message, array $context = [], ?string $channelName = null): void
    {
        self::get($channelName)->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     */
    public static function info(string|\Stringable|\Throwable $message, array $context = [], ?string $channelName = null): void
    {
        self::get($channelName)->info($message, $context);
    }

    /**
     * Detailed debug information.
     */
    public static function debug(string|\Stringable|\Throwable $message, array $context = [], ?string $channelName = null): void
    {
        self::get($channelName)->debug($message, $context);
    }
}
