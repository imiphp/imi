<?php

declare(strict_types=1);

namespace Imi\Workerman\Process;

use Imi\App;
use Imi\Event\Event;
use Imi\Log\Handler\ConsoleHandler;
use Imi\Log\Log;
use Imi\Log\Logger;
use Imi\RequestContext;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use Imi\Workerman\Process\Contract\IProcess;
use Imi\Workerman\Server\WorkermanServerWorker;
use Symfony\Component\Console\Output\StreamOutput;
use Workerman\Worker;

/**
 * 进程管理类.
 */
class ProcessManager
{
    private static array $map = [];

    /**
     * 进程数组.
     *
     * @var Worker[]
     */
    private static array $processes = [];

    private function __construct()
    {
    }

    public static function getMap(): array
    {
        return self::$map;
    }

    public static function setMap(array $map): void
    {
        self::$map = $map;
    }

    /**
     * 增加映射关系.
     */
    public static function add(string $name, string $className, array $options): void
    {
        self::$map[$name] = [
            'className' => $className,
            'options'   => $options,
        ];
    }

    /**
     * 获取配置.
     */
    public static function get(string $name): ?array
    {
        return self::$map[$name] ?? null;
    }

    /**
     * 实例化新的进程.
     */
    public static function newProcess(string $name, array $args = [], ?string $alias = null): Worker
    {
        $options = self::get($name);
        if (null === $options)
        {
            throw new \RuntimeException(sprintf('Process %s config not found', $name));
        }
        if (isset(self::$processes[$name]))
        {
            throw new \RuntimeException(sprintf('Process %s is exists', $name));
        }
        $processName = $alias ?? $name;
        self::$processes[$processName] = $worker = new WorkermanServerWorker();
        $worker->name = $processName;
        $worker->reloadable = false;
        $worker->onWorkerStart = static function (Worker $worker) use ($args, $processName, $options) {
            // 随机数播种
            mt_srand();

            App::set(ProcessAppContexts::PROCESS_TYPE, ProcessType::PROCESS, true);
            App::set(ProcessAppContexts::PROCESS_NAME, $processName, true);

            if (WorkermanServerWorker::$daemonize)
            {
                /** @var Logger $loggerInstance */
                $loggerInstance = App::getBean('Logger');
                foreach ($loggerInstance->getLoggers() as $logger)
                {
                    foreach ($logger->getHandlers() as $handler)
                    {
                        if ($handler instanceof ConsoleHandler)
                        {
                            $handler->setOutput($stdoutStream ??= new StreamOutput(fopen(WorkermanServerWorker::$stdoutFile, 'a')));
                        }
                    }
                }
            }

            Log::info('Process start [' . $processName . ']. <info>pid: </info>' . getmypid());

            RequestContext::muiltiSet([
                'worker' => $worker,
            ]);

            // 进程开始事件
            Event::trigger('IMI.PROCESS.BEGIN', [
                'name'    => $processName,
                'process' => $worker,
            ]);
            try
            {
                // 执行任务
                /** @var IProcess $processInstance */
                $processInstance = App::getBean($options['className'], $args);
                $processInstance->run($worker);
            }
            catch (\Throwable $th)
            {
                // @phpstan-ignore-next-line
                App::getBean('ErrorLog')->onException($th);
            }
            finally
            {
                // 进程结束事件
                Event::trigger('IMI.PROCESS.END', [
                    'name'    => $processName,
                    'process' => $worker,
                ]);
                Log::info('Process stop [' . $processName . ']. <info>pid: </info>' . getmypid());
            }
        };

        return $worker;
    }

    /**
     * 获取进程.
     */
    public static function getProcess(string $name): Worker
    {
        if (!isset(self::$processes[$name]))
        {
            throw new \RuntimeException(sprintf('Process %s not found', $name));
        }

        return self::$processes[$name];
    }
}
