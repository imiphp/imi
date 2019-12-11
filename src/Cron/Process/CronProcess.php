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

    public function run(\Swoole\Process $process)
    {
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
            $this->socket = stream_socket_server('unix://' . $socketFile, $errno, $errstr);
            if(false === $this->socket)
            {
                throw new \RuntimeException(sprintf('Create unix socket server failed, errno: %s, errstr: %s, file: %', $errno, $errstr, $socketFile));
            }
            $this->startSchedule();
            while(true)
            {
                $arrRead = [$this->socket];
                $arrWrite = [];
                if(stream_select($arrRead, $arrWrite, $arrWrite, null))
                {
                    $conn = stream_socket_accept($this->socket, 1);
                    if(false === $conn)
                    {
                        continue;
                    }
                    imigo(function() use($conn){
                        $this->parseConn($conn);
                    });
                }
            }
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
        while(true)
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
                if(!$result instanceof Result)
                {
                    return;
                }
                $this->scheduler->completeTask($result);
            } catch(\Throwable $th) {
                $this->errorLog->onException($th);
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
            do {
                $time = microtime(true);
    
                foreach($this->scheduler->schedule() as $task)
                {
                    $this->scheduler->runTask($task);
                }
    
                $sleep = 1 - (microtime(true) - $time);
                if($sleep > 0)
                {
                    usleep($sleep * 1000000);
                }
            } while(true);
        });
    }

}
