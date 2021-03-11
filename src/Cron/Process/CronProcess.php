<?php

namespace Imi\Cron\Process;

use Imi\Aop\Annotation\Inject;
use Imi\Cron\Message\AddCron;
use Imi\Cron\Message\Clear;
use Imi\Cron\Message\RemoveCron;
use Imi\Cron\Message\Result;
use Imi\Process\Annotation\Process;
use Imi\Process\BaseProcess;

/**
 * 定时任务进程.
 *
 * @Process(name="CronProcess", co=false)
 */
class CronProcess extends BaseProcess
{
    /**
     * @Inject("CronScheduler")
     *
     * @var \Imi\Cron\Scheduler
     */
    protected $scheduler;

    /**
     * @Inject("ErrorLog")
     *
     * @var \Imi\Log\ErrorLog
     */
    protected $errorLog;

    /**
     * @Inject("CronManager")
     *
     * @var \Imi\Cron\CronManager
     */
    protected $cronManager;

    /**
     * socket 资源.
     *
     * @var resource
     */
    protected $socket;

    /**
     * 是否正在运行.
     *
     * @var bool
     */
    protected $running = false;

    public function run(\Swoole\Process $process)
    {
        \Imi\Util\Process::signal(\SIGTERM, function ($signo) {
            $this->stop();
        });
        \Swoole\Runtime::enableCoroutine();
        $this->startSocketServer();
    }

    /**
     * @return void
     */
    protected function startSocketServer()
    {
        imigo(function () {
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
        });
    }

    /**
     * 处理连接.
     *
     * @param resource $conn
     *
     * @return void
     */
    protected function parseConn($conn)
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
     *
     * @return void
     */
    protected function startSchedule()
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
            } while ($running);
        });
    }

    /**
     * 停止.
     *
     * @return void
     */
    protected function stop()
    {
        $this->running = false;
        $this->scheduler->close();
    }
}
