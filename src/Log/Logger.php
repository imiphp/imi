<?php

declare(strict_types=1);

namespace Imi\Log;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Config;
use Imi\Util\ClassObject;
use InvalidArgumentException;
use Monolog\Handler\FormattableHandlerInterface;

/**
 * @Bean("Logger")
 */
class Logger
{
    /**
     * @var MonoLogger[]
     */
    private array $loggers = [];

    /**
     * @return MonoLogger[]
     */
    public function getLoggers(): array
    {
        return $this->loggers;
    }

    public function getLogger(?string $channelName = null): MonoLogger
    {
        $config = null;
        if (null === $channelName)
        {
            $config = Config::get('@app.logger', []);
            $channelName = $config['default'] ?? 'imi';
        }
        if (!isset($this->loggers[$channelName]))
        {
            $config ??= Config::get('@app.logger', []);
            $channelsConfig = $config['channels'] ?? [];
            if (!isset($channelsConfig[$channelName]))
            {
                throw new InvalidArgumentException(sprintf('Logger %s not found', $channelName));
            }
            $channelConfig = $channelsConfig[$channelName];
            $loggerClass = $config['logger'] ?? MonoLogger::class;
            $logger = $this->loggers[$channelName] = new $loggerClass($channelName);
            $handlers = [];
            $app = App::getApp();
            foreach ($channelConfig['handlers'] ?? [] as $handlerConfig)
            {
                if (!isset($handlerConfig['class']))
                {
                    throw new InvalidArgumentException('Logger handler must have class');
                }
                if (isset($handlerConfig['env']))
                {
                    if (!\in_array($app->getType(), $handlerConfig['env']))
                    {
                        continue;
                    }
                }
                $handler = $handlers[] = ClassObject::newInstance($handlerConfig['class'], $handlerConfig['construct'] ?? []);
                if (isset($handlerConfig['formatter']) && $handler instanceof FormattableHandlerInterface)
                {
                    $formatterConfig = $handlerConfig['formatter'];
                    $formatter = ClassObject::newInstance($formatterConfig['class'], $formatterConfig['construct'] ?? []);
                    $handler->setFormatter($formatter);
                }
            }
            $logger->setHandlers($handlers);

            return $logger;
        }

        return $this->loggers[$channelName];
    }

    public function clear(): void
    {
        $this->loggers = [];
    }
}
