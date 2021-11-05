<?php

declare(strict_types=1);

namespace Imi\Swoole\Cron\Process;

use Imi\Aop\Annotation\Inject;
use Imi\Cron\Contract\ICronManager;
use Imi\Cron\Contract\IScheduler;
use Imi\Cron\CronManager;
use Imi\Cron\Message\AddCron;
use Imi\Cron\Message\Clear;
use Imi\Cron\Message\RemoveCron;
use Imi\Cron\Message\Result;
use Imi\Log\ErrorLog;
use Imi\Swoole\Process\Annotation\Process;
use Imi\Swoole\Process\BaseProcess;
use Swoole\Coroutine\System;

/**
 * 定时任务进程.
 *
 * @Process(name="CronProcess")
 */
class CronProcess extends BaseProcess
{
    /**
     * @Inject("CronScheduler")
     */
    protected IScheduler $scheduler;

    /**
     * @Inject("ErrorLog")
     */
    protected ErrorLog $errorLog;

    /**
     * @Inject("CronManager")
     */
    protected ICronManager $cronManager;

    /**
     * socket 资源.
     *
     * @var resource
     */
    protected $socket;

    /**
     * 是否正在运行.
     */
    protected bool $running = false;

    public function run(\Swoole\Process $process): void
    {
        imigo(function () {
            if (System::waitSignal(\SIGTERM))
            {
                $this->stop();
            }
        });
        $this->startSocketServer();
    }

    protected function startSocketServer(): void
    {
        $socketFile = $this->cronManager->getSocketFile();
        if (is_file($socketFile))
        {
            unlink($socketFile);
        }
        $this->socket = $socket = stream_socket_server('unix://' . $socketFile, $errno, $errstr);
        if (false === $socket)
        {
            throw new \RuntimeException(sprintf('Create unix socket server failed, errno: %s, errstr: %s, file: %s', $errno, $errstr, $socketFile));
        }
        $this->running = true;
        $running = &$this->running;
        $this->startSchedule();
        // @phpstan-ignore-next-line
        while ($running)
        {
            $arrRead = [$socket];
            $write = $except = [];
            if (stream_select($arrRead, $write, $except, 3) > 0)
            {
                $conn = stream_socket_accept($socket, 1);
                if (false === $conn)
                {
                    continue;
                }
                imigo(function () use ($conn) {
                    $this->parseConn($conn);
                    fclose($conn);
                });
            }
        }
        // @phpstan-ignore-next-line
        fclose($socket);
    }

    /**
     * 处理连接.
     *
     * @param resource $conn
     */
    protected function parseConn($conn): void
    {
        $running = &$this->running;
        $scheduler = $this->scheduler;
        $errorLog = $this->errorLog;
        while ($running)
        {
            try
            {
                $meta = fread($conn, 4);
                if ('' === $meta || false === $meta)
                {
                    return;
                }
                $length = unpack('N', $meta)[1];
                $data = fread($conn, $length);
                if (false === $data || !isset($data[$length - 1]))
                {
                    return;
                }
                $result = unserialize($data);
                if ($result instanceof Result)
                {
                    $scheduler->completeTask($result);
                }
                elseif ($result instanceof AddCron)
                {
                    $cronAnnotation = $result->cronAnnotation;
                    $this->cronManager->addCronByAnnotation($cronAnnotation, $result->task);
                }
                elseif ($result instanceof RemoveCron)
                {
                    $this->cronManager->removeCron($result->id);
                }
                elseif ($result instanceof Clear)
                {
                    $this->cronManager->clear();
                }
            }
            catch (\Throwable $th)
            {
                $errorLog->onException($th);
            }
        }
    }

    /**
     * 开始定时任务调度.
     */
    protected function startSchedule(): void
    {
        imigo(function () {
            $scheduler = $this->scheduler;
            $running = &$this->running;
            do
            {
                $time = microtime(true);

                foreach ($scheduler->schedule() as $task)
                {
                    $scheduler->runTask($task);
                }

                $sleep = 1 - (microtime(true) - $time);
                if ($sleep > 0)
                {
                    usleep((int) ($sleep * 1000000));
                }
            }
            while ($running);
        });
    }

    /**
     * 停止.
     */
    protected function stop(): void
    {
        $this->running = false;
        $this->scheduler->close();
    }
}
