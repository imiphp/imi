<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

use Imi\App;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Server\ServerManager;
use Imi\Swoole\Process\Contract\IProcess;
use Imi\Swoole\Process\Exception\ProcessAlreadyRunException;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Util\Coroutine;
use Imi\Swoole\Util\Imi as SwooleImi;
use Imi\Timer\Timer;
use Imi\Util\File;
use Imi\Util\Imi;
use Imi\Util\ImiPriority;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use Swoole\Event as SwooleEvent;
use Swoole\ExitException;
use Swoole\Table;

use function Imi\ttyExec;

/**
 * 进程管理类.
 */
class ProcessManager
{
    use \Imi\Util\Traits\TStaticClass;

    private static array $map = [];

    /**
     * 锁集合.
     */
    private static array $lockMap = [];

    /**
     * 挂载在管理进程下的进程列表.
     *
     * @var Process[]
     */
    private static array $managerProcesses = [];

    /**
     * 挂载在管理进程下的进程列表.
     *
     * @var array<string, array{name: string, alias: string, process: Process}>
     */
    private static array $managerProcessSet = [];

    private static Table $processInfoTable;

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
     * 创建进程
     * 本方法无法在控制器中使用
     * 返回 Process 对象实例.
     */
    public static function create(string $name, array $args = [], ?bool $redirectStdinStdout = null, ?int $pipeType = null, ?string $alias = null, bool $runWithManager = false): Process
    {
        $processOption = self::get($name);
        if (null === $processOption)
        {
            throw new \RuntimeException(sprintf('Process %s not found', $name));
        }
        if ($processOption['options']['unique'] && static::isRunning($name))
        {
            throw new ProcessAlreadyRunException(sprintf('Process %s already run', $name));
        }
        if (null === $redirectStdinStdout)
        {
            $redirectStdinStdout = $processOption['options']['redirectStdinStdout'];
        }
        if (null === $pipeType)
        {
            $pipeType = $processOption['options']['pipeType'];
        }

        return new Process(static::getProcessCallable($args, $name, $processOption, $alias, $runWithManager), $redirectStdinStdout, $pipeType);
    }

    /**
     * 获取进程回调.
     */
    public static function getProcessCallable(array $args, string $name, array $processOption, ?string $alias = null, bool $runWithManager = false): callable
    {
        return static function (Process $swooleProcess) use ($args, $name, $processOption, $alias, $runWithManager): void {
            App::set(ProcessAppContexts::PROCESS_TYPE, ProcessType::PROCESS, true);
            App::set(ProcessAppContexts::PROCESS_NAME, $name, true);
            // 设置进程名称
            $processName = $name;
            if ($alias)
            {
                $processName .= '#' . $alias;
            }
            SwooleImi::setProcessName('process', [
                'processName'   => $processName,
            ]);
            // 随机数播种
            mt_srand();
            Imi::loadRuntimeInfo(Imi::getCurrentModeRuntimePath('runtime'));
            $exitCode = 0;
            $callable = static function () use ($swooleProcess, $args, $name, $alias, $processOption, &$exitCode, $runWithManager): void {
                if ($runWithManager)
                {
                    Log::info('Process start [' . $name . ']. pid: ' . getmypid() . ', UnixSocket: ' . $swooleProcess->getUnixSocketFile());
                }
                // 超时强制退出
                Event::on('IMI.PROCESS.END', static fn () => Timer::after(3000, static fn () => SwooleEvent::exit()), ImiPriority::IMI_MAX);
                // 正常退出
                Event::on('IMI.PROCESS.END', static fn () => Signal::clear(), ImiPriority::IMI_MIN);
                $processEnded = false;
                imigo(static function () use ($name, $swooleProcess, &$processEnded): void {
                    if (Signal::wait(\SIGTERM))
                    {
                        if ($processEnded)
                        {
                            return;
                        }
                        $processEnded = true;
                        // 进程结束事件
                        Event::trigger('IMI.PROCESS.END', [
                            'name'      => $name,
                            'process'   => $swooleProcess,
                        ]);
                    }
                });
                if ($inCoroutine = Coroutine::isIn())
                {
                    Coroutine::defer(static function () use ($name, $swooleProcess, &$processEnded): void {
                        if ($processEnded)
                        {
                            return;
                        }
                        $processEnded = true;
                        // 进程结束事件
                        Event::trigger('IMI.PROCESS.END', [
                            'name'      => $name,
                            'process'   => $swooleProcess,
                        ]);
                    });
                }
                try
                {
                    if ($processOption['options']['unique'] && !self::lockProcess($name))
                    {
                        throw new \RuntimeException(sprintf('Lock process %s failed', $name));
                    }
                    if ($processOption['options']['co'])
                    {
                        $swooleProcess->startUnixSocketServer();
                    }
                    // 写出进程信息
                    if (null !== $swooleProcess->id && null !== $swooleProcess->pid)
                    {
                        self::writeProcessInfo(self::buildUniqueId($name, $alias), $swooleProcess->id, $swooleProcess->pid);
                    }
                    // 进程开始事件
                    Event::trigger('IMI.PROCESS.BEGIN', [
                        'name'      => $name,
                        'process'   => $swooleProcess,
                    ]);
                    // 执行任务
                    /** @var IProcess $processInstance */
                    $processInstance = App::newInstance($processOption['className'], $args);
                    $processInstance->run($swooleProcess);
                    if ($processOption['options']['unique'])
                    {
                        self::unlockProcess($name);
                    }
                }
                catch (ExitException $e)
                {
                    $exitCode = $e->getStatus();
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                    $exitCode = 255;
                }
                finally
                {
                    if (!$inCoroutine && !$processEnded)
                    {
                        $processEnded = true;
                        // 进程结束事件
                        Event::trigger('IMI.PROCESS.END', [
                            'name'      => $name,
                            'process'   => $swooleProcess,
                        ]);
                    }
                    if ($runWithManager)
                    {
                        Log::info('Process stop [' . $name . ']. pid: ' . getmypid());
                    }
                }
            };
            if ($processOption['options']['co'])
            {
                // 强制开启进程协程化
                \Swoole\Coroutine\run($callable);
            }
            else
            {
                $callable();
            }
            if (0 != $exitCode)
            {
                exit($exitCode);
            }
        };
    }

    /**
     * 进程是否已在运行，只有unique为true时有效.
     */
    public static function isRunning(string $name): bool
    {
        $processOption = self::get($name);
        if (null === $processOption)
        {
            return false;
        }
        if (!$processOption['options']['unique'])
        {
            return false;
        }
        $fileName = self::getLockFileName($name);
        if (!is_file($fileName))
        {
            return false;
        }
        $fp = fopen($fileName, 'w+');
        if (false === $fp)
        {
            return false;
        }
        if (!flock($fp, \LOCK_EX | \LOCK_NB))
        {
            fclose($fp);

            return true;
        }
        flock($fp, \LOCK_UN);
        fclose($fp);
        unlink($fileName);

        return false;
    }

    /**
     * 运行进程，协程挂起等待进程执行返回
     * 执行完成返回数组，包含了进程退出的状态码、信号。
     *
     * @param bool $stdOutput 输出控制：true，打印到终端，不返回、false，不输出终端，返回输出
     *
     * @return array{code: int, signal: int}
     */
    public static function run(string $name, array $args = [], ?bool $redirectStdinStdout = null, ?int $pipeType = null, bool $stdOutput = false): array
    {
        if (null !== $redirectStdinStdout)
        {
            $args['redirectStdinStdout'] = $redirectStdinStdout;
        }
        if (null !== $pipeType)
        {
            $args['pipeType'] = $pipeType;
        }
        $cmd = Imi::getImiCmd('process/run', [$name], $args);

        if ($stdOutput)
        {
            /** @var \Symfony\Component\Process\Process $process */
            $process = null;
            ttyExec($cmd, null, $process);

            return [
                'code'   => $process->getExitCode(),
                'signal' => $process->isTerminated() ? $process->getTermSignal() : $process->getStopSignal(),
                'output' => '',
            ];
        }
        else
        {
            return Coroutine::exec($cmd);
        }
    }

    /**
     * 运行进程，创建一个协程执行进程，无法获取进程执行结果
     * 执行失败返回false，执行成功返回数组，包含了进程退出的状态码、信号、输出内容。
     * array(
     *     'code'   => 0,
     *     'signal' => 0,
     *     'output' => '',
     * );.
     */
    public static function coRun(string $name, array $args = [], ?bool $redirectStdinStdout = null, ?int $pipeType = null): void
    {
        Coroutine::create(static function () use ($name, $args, $redirectStdinStdout, $pipeType): void {
            static::run($name, $args, $redirectStdinStdout, $pipeType);
        });
    }

    /**
     * 挂靠Manager进程运行进程.
     */
    public static function runWithManager(string $name, array $args = [], ?bool $redirectStdinStdout = null, ?int $pipeType = null, ?string $alias = null): ?Process
    {
        $alias ??= $name;
        $process = static::create($name, $args, $redirectStdinStdout, $pipeType, $alias, true);
        $swooleServer = ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer();
        $swooleServer->addProcess($process);
        self::$managerProcesses[$name][$alias] = $process;
        self::$managerProcessSet[self::buildUniqueId($name, $alias)] = [
            'name'    => $name,
            'alias'   => $alias,
            'process' => $process,
        ];

        return $process;
    }

    public static function buildUniqueId(string $name, ?string $alias): string
    {
        return hash('md5', "{$name}|{$alias}");
    }

    public static function initProcessInfoTable(): void
    {
        $count = \count(self::$managerProcessSet);
        // @phpstan-ignore-next-line
        $table = new Table($count * 2);
        // @phpstan-ignore-next-line
        $table->column('wid', Table::TYPE_INT);
        // @phpstan-ignore-next-line
        $table->column('pid', Table::TYPE_INT);
        $table->create();
        self::$processInfoTable = $table;
    }

    public static function writeProcessInfo(string $id, int $wid, int $pid): void
    {
        if (!isset(self::$processInfoTable))
        {
            return;
        }
        self::$processInfoTable->set($id, [
            'wid' => $wid,
            'pid' => $pid,
        ]);
    }

    /**
     * @return array{wid: int, pid: int}|null
     */
    public static function readProcessInfo(string $id): ?array
    {
        if (!isset(self::$processInfoTable))
        {
            return null;
        }

        // @phpstan-ignore-next-line
        return self::$processInfoTable->get($id) ?: null;
    }

    /**
     * 获取挂载在管理进程下的进程.
     */
    public static function getProcessWithManager(string $name, ?string $alias = null): ?Process
    {
        $alias ??= $name;

        return self::$managerProcesses[$name][$alias] ?? null;
    }

    /**
     * 获取挂载在管理进程下的进程列表.
     *
     * @return array<string, array{name: string, alias: string, process: Process}>
     */
    public static function getProcessSetWithManager(): array
    {
        return self::$managerProcessSet;
    }

    /**
     * 锁定进程，实现unique.
     */
    private static function lockProcess(string $name): bool
    {
        $fileName = self::getLockFileName($name);
        $fp = fopen($fileName, 'w+');
        if (false === $fp)
        {
            return false;
        }
        if (!flock($fp, \LOCK_EX | \LOCK_NB))
        {
            fclose($fp);

            return false;
        }
        self::$lockMap[$name] = [
            'fileName'  => $fileName,
            'fp'        => $fp,
        ];

        return true;
    }

    /**
     * 解锁进程，实现unique.
     */
    private static function unlockProcess(string $name): bool
    {
        $lockMap = &self::$lockMap;
        if (!isset($lockMap[$name]))
        {
            return false;
        }
        $lockItem = $lockMap[$name];
        $fp = $lockItem['fp'];
        if (flock($fp, \LOCK_UN) && fclose($fp))
        {
            unlink($lockItem['fileName']);
            unset($lockMap[$name]);

            return true;
        }

        return false;
    }

    /**
     * 获取文件锁的文件名.
     */
    private static function getLockFileName(string $name): string
    {
        $path = Imi::getRuntimePath(str_replace('\\', '-', App::getNamespace()), 'processLock');
        File::createDir($path);

        return File::path($path, $name . '.lock');
    }
}
