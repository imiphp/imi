<?php
namespace Imi\Cron\Process;

use Imi\Cron\Message\Result;
use Imi\Process\BaseProcess;
use Imi\Aop\Annotation\Inject;
use Imi\Process\Annotation\Process;

/**
 * 定时任务进程
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
     * socket 资源
     *
     * @var resource
     */
    private $socket;

    /**
     * 是否正在运行
     *
     * @var boolean
     */
    private $running = false;

    public function run(\Swoole\Process $process)
    {
        \Swoole\Process::signal(SIGTERM, function($signo) {
            $this->running = false;
            $this->scheduler->close();
        });
        $this->startSocketServer();
    }

    private function startSocketServer()
    {
        imigo(function(){
            $socketFile = $this->cronManager->getSocketFile();
            if(is_file($socketFile))
            {
                unlink($socketFile);
            }
            $this->socket = $socket = stream_socket_server('unix://' . $socketFile, $errno, $errstr);
            if(false === $socket)
            {
                throw new \RuntimeException(sprintf('Create unix socket server failed, errno: %s, errstr: %s, file: %', $errno, $errstr, $socketFile));
            }
            $this->running = true;
            $running = &$this->running;
            $this->startSchedule();
            while($running)
            {
                $arrRead = [$socket];
                $write = $except = null;
                if(stream_select($arrRead, $write, $except, 3) > 0)
                {
                    $conn = stream_socket_accept($socket, 1);
                    if(false === $conn)
                    {
                        continue;
                    }
                    imigo(function() use($conn){
                        $this->parseConn($conn);
                        fclose($conn);
                    });
                }
            }
            fclose($socket);
        });
    }

    /**
     * 处理连接
     *
     * @param resource $conn
     * @return void
     */
    private function parseConn($conn)
    {
        $running = &$this->running;
        $scheduler = $this->scheduler;
        $errorLog = $this->errorLog;
        while($running)
        {
            try {
                $meta = fread($conn, 4);
                if('' === $meta || false === $meta)
                {
                    return;
                }
                $length = unpack('N', $meta)[1];
                $data = fread($conn, $length);
                if(false === $data || !isset($data[$length - 1]))
                {
                    return;
                }
                $result = unserialize($data);
                if($result instanceof Result)
                {
                    $scheduler->completeTask($result);
                }
            } catch(\Throwable $th) {
                $errorLog->onException($th);
            }
        }
    }

    /**
     * 开始定时任务调度
     *
     * @return void
     */
    private function startSchedule()
    {
        imigo(function(){
            $scheduler = $this->scheduler;
            $running = &$this->running;
            do {
                $time = microtime(true);
    
                foreach($scheduler->schedule() as $task)
                {
                    $scheduler->runTask($task);
                }
    
                $sleep = 1 - (microtime(true) - $time);
                if($sleep > 0)
                {
                    usleep($sleep * 1000000);
                }
            } while($running);
        });
    }

}
