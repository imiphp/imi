<?php

declare(strict_types=1);

namespace Imi\Workerman\Server;

use Imi\Util\Imi;
use Imi\Workerman\Util\Imi as UtilImi;
use Workerman\Worker;

class WorkermanServerWorker extends Worker
{
    /**
     * {@inheritDoc}
     */
    public function __construct(string $socket_name = '', array $context_option = [])
    {
        parent::__construct($socket_name, $context_option);

        if (OS_TYPE_LINUX === static::$_OS  // if linux
            && version_compare(\PHP_VERSION, '7.0.0', 'ge') // if php >= 7.0.0
            && version_compare(php_uname('r'), '3.9', 'ge') // if kernel >=3.9
            && 'darwin' !== strtolower(php_uname('s')) // if not Mac OS
            && !str_starts_with($socket_name, 'unix')) // if not unix socket
        {
            $this->reusePort = true;
        }
    }

    public static function getMasterPid(): int
    {
        return static::$_masterPid;
    }

    /**
     * {@inheritDoc}
     */
    protected static function init(): void
    {
        parent::init();
        static::$_startFile = Imi::getCurrentModeRuntimePath('start_file');
        UtilImi::setProcessName('master');
    }

    public static function clearAll(): void
    {
        static::$daemonize = false;
        static::$stdoutFile = '/dev/null';
        static::$pidFile = '';
        static::$logFile = '';
        // @phpstan-ignore-next-line
        static::$globalEvent = null;
        // @phpstan-ignore-next-line
        static::$onMasterReload = null;
        // @phpstan-ignore-next-line
        static::$onMasterStop = null;
        static::$eventLoopClass = '';
        static::$processTitle = 'WorkerMan';
        static::$_masterPid = 0;
        static::$_workers = [];
        static::$_pidMap = [];
        static::$_pidsToRestart = [];
        static::$_idMap = [];
        static::$_status = static::STATUS_STARTING;
        static::$_maxWorkerNameLength = 12;
        static::$_maxSocketNameLength = 12;
        static::$_maxUserNameLength = 12;
        static::$_maxProtoNameLength = 4;
        static::$_maxProcessesNameLength = 9;
        static::$_maxStatusNameLength = 1;
        static::$_statisticsFile = '';
        static::$_startFile = '';
        static::$_processForWindows = [];
        static::$_globalStatistics = [
            'start_timestamp'  => 0,
            'worker_exit_info' => [],
        ];
        static::$_availableEventLoops = [
            'event'    => \Workerman\Events\Event::class,
            'libevent' => \Workerman\Events\Libevent::class,
        ];
        static::$_builtinTransports = [
            'tcp'   => 'tcp',
            'udp'   => 'udp',
            'unix'  => 'unix',
            'ssl'   => 'tcp',
        ];
        static::$_errorType = [
            \E_ERROR             => 'E_ERROR',             // 1
            \E_WARNING           => 'E_WARNING',           // 2
            \E_PARSE             => 'E_PARSE',             // 4
            \E_NOTICE            => 'E_NOTICE',            // 8
            \E_CORE_ERROR        => 'E_CORE_ERROR',        // 16
            \E_CORE_WARNING      => 'E_CORE_WARNING',      // 32
            \E_COMPILE_ERROR     => 'E_COMPILE_ERROR',     // 64
            \E_COMPILE_WARNING   => 'E_COMPILE_WARNING',   // 128
            \E_USER_ERROR        => 'E_USER_ERROR',        // 256
            \E_USER_WARNING      => 'E_USER_WARNING',      // 512
            \E_USER_NOTICE       => 'E_USER_NOTICE',       // 1024
            \E_STRICT            => 'E_STRICT',            // 2048
            \E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR', // 4096
            \E_DEPRECATED        => 'E_DEPRECATED',        // 8192
            \E_USER_DEPRECATED   => 'E_USER_DEPRECATED',   // 16384
        ];
        static::$_gracefulStop = false;
        // @phpstan-ignore-next-line
        static::$_outputStream = null;
        // @phpstan-ignore-next-line
        static::$_outputDecorated = null;
        static::$statusFile = '';
    }

    /**
     * {@inheritDoc}
     */
    protected static function displayUI(): void
    {
        // BUG: https://github.com/walkor/workerman/pull/708
        // 此处为修复低版本 bug
        $tmpArgv = $GLOBALS['argv'];
        parent::displayUI();
        $GLOBALS['argv'] = $tmpArgv;
    }

    /**
     * Check master process is alive.
     *
     * @param int $master_pid
     */
    protected static function checkMasterIsAlive(mixed $master_pid): bool
    {
        if (empty($master_pid))
        {
            return false;
        }

        // @phpstan-ignore-next-line
        $master_is_alive = $master_pid && posix_kill((int) $master_pid, 0) && posix_getpid() !== $master_pid;
        if (!$master_is_alive)
        {
            return false;
        }

        $cmdline = "/proc/{$master_pid}/cmdline";
        if (!is_readable($cmdline) || empty(static::$processTitle))
        {
            return true;
        }

        $content = file_get_contents($cmdline);
        if (empty($content))
        {
            return true;
        }

        return false !== stripos($content, 'imi') || false !== stripos($content, 'php');
    }
}
