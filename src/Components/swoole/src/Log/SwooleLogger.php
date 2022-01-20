<?php

declare(strict_types=1);

namespace Imi\Swoole\Log;

use Imi\Cli\ImiCommand;
use Imi\Config;
use Imi\Event\Event;
use Imi\Log\MonoLogger;
use Monolog\DateTimeImmutable;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class SwooleLogger extends MonoLogger
{
    public const KEY_IMI_ASYNC_DATETIME = '__imi_async_datetime__';

    protected ?Channel $logChannel = null;

    protected bool $async = false;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        if ($this->async = Config::get('@app.logger.async', false))
        {
            $this->pushProcessor(function (array $record): array {
                if (isset($context[self::KEY_IMI_ASYNC_DATETIME]))
                {
                    $record['datetime'] = $context[self::KEY_IMI_ASYNC_DATETIME];
                    unset($context[self::KEY_IMI_ASYNC_DATETIME]);
                }

                return $record;
            });
            $this->logChannel = new Channel(Config::get('@app.logger.asyncQueueLength', 1024));
            Coroutine::create(function () {
                $this->__logProcessor();
            });
            Event::on(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.PROCESS.END', 'IMI.SWOOLE.MAIN_COROUTINE.END'], function () {
                $this->logChannel->close();
                $this->logChannel = null;
            }, \Imi\Util\ImiPriority::IMI_MIN);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addRecord(int $level, string $message, array $context = []): bool
    {
        if ($this->async && $this->logChannel)
        {
            $context[self::KEY_IMI_ASYNC_DATETIME] = new DateTimeImmutable($this->microsecondTimestamps, $this->timezone);

            return $this->logChannel->push([$level, $message, $context]);
        }
        else
        {
            return parent::addRecord($level, $message, $context);
        }
    }

    protected function __logProcessor(): void
    {
        $channel = $this->logChannel;
        while ($args = $channel->pop())
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
