<?php

declare(strict_types=1);

namespace Imi\Swoole\Log;

use Imi\Cli\ImiCommand;
use Imi\Config;
use Imi\Event\Event;
use Imi\Log\MonoLogger;
use Imi\Swoole\Util\Coroutine;
use Monolog\DateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;
use Swoole\Coroutine\Channel;

class SwooleLogger extends MonoLogger
{
    public const KEY_IMI_ASYNC_DATETIME = '__imi_async_datetime__';

    protected ?Channel $logChannel = null;

    protected bool $async = false;

    protected bool $asyncLogging = false;

    /**
     * @param mixed ...$args
     */
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        if ($this->async = Config::get('@app.logger.async', false) && Coroutine::isIn())
        {
            $this->pushProcessor(static function (LogRecord $record): LogRecord {
                if (isset($record->context[self::KEY_IMI_ASYNC_DATETIME]))
                {
                    $context = $record->context;
                    unset($context[self::KEY_IMI_ASYNC_DATETIME]);
                    $record = $record->with(datetime: $record->context[self::KEY_IMI_ASYNC_DATETIME], context: $context);
                }

                return $record;
            });
            $this->logChannel = new Channel(Config::get('@app.logger.asyncQueueLength', 1024));
            Coroutine::create(function (): void {
                $this->__logProcessor();
            });
            Event::on(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.PROCESS.END', 'IMI.SWOOLE.MAIN_COROUTINE.END', 'IMI.QUICK_START_AFTER'], function (): void {
                $this->asyncLogging = false;
                $this->logChannel->close();
            }, \Imi\Util\ImiPriority::IMI_MIN + 1);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addRecord(int|Level $level, string $message, array $context = [], DateTimeImmutable $datetime = null): bool
    {
        if ($this->asyncLogging)
        {
            $context[self::KEY_IMI_ASYNC_DATETIME] = new DateTimeImmutable($this->microsecondTimestamps, $this->timezone);

            return $this->logChannel->push([$level, $message, $context, $datetime]);
        }
        else
        {
            return parent::addRecord($level, $message, $context, $datetime);
        }
    }

    protected function __logProcessor(): void
    {
        $this->asyncLogging = true;
        while ($args = $this->logChannel->pop())
        {
            try
            {
                parent::addRecord(...$args);
            }
            catch (\Throwable $th)
            {
                // 这里不能再调用日志记录，否则可能产生死循环
                ImiCommand::getOutput()->writeln('Async logProcessor error: ' . $th->getMessage());
            }
        }
    }
}
