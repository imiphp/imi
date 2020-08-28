<?php
namespace Imi\Server;

use Imi\Config;
use Imi\Util\Imi;
use Swoole\Process;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Process\Pool;
use Imi\ServerManage;
use Imi\Process\Pool\InitEventParam;
use Imi\Process\Pool\WorkerEventParam;
use Imi\Server\Event\Param\StartEventParam;
use Imi\Server\Event\Param\WorkerExitEventParam;
use Imi\Server\Event\Param\WorkerStopEventParam;
use Imi\Server\Event\Param\WorkerStartEventParam;

class CoServer
{
    /**
     * 是否正在运行
     *
     * @var boolean
     */
    private $running = false;

    /**
     * 配置
     *
     * @var array
     */
    private $config;

    /**
     * 服务器名
     *
     * @var string
     */
    private $name;

    /**
     * 工作进程数量
     * 处理请求的进程
     *
     * @var int
     */
    private $workerNum;

    /**
     * 实际的进程数量
     *
     * @var int
     */
    private $realWorkerNum;

    /**
     * 用户自定义进程数量
     *
     * @var integer
     */
    private $processNum = 0;

    /**
     * 父进程ID
     *
     * @var int
     */
    private $pid;

    /**
     * 工作进程ID
     *
     * @var integer
     */
    private $workerId = -1;

    /**
     * 用户自定义进程回调列表
     *
     * @var callable[]
     */
    private $processes = [];

    public function __construct($name, $workerNum)
    {
        $this->name = $name;
        $this->workerNum = $workerNum;
        $this->pid = getmypid();
        $this->loadConfig();
        if($workerNum)
        {
            $this->workerNum = $workerNum;
        }
        else if(!($this->workerNum = $this->config['configs']['worker_num'] ?? null))
        {
            $this->workerNum = swoole_cpu_num();
        }
        $this->checkReusePort();
    }

    public function run()
    {
        if($this->running)
        {
            return;
        }
        $this->running = true;

        Event::trigger('IMI.MAIN_SERVER.START', [
            'server'    => null,
            'name'      => $this->name,
            'workerNum' => $this->workerNum,
            'config'    => $this->config,
        ], $this, StartEventParam::class);

        Event::trigger('IMI.CO_SERVER.START', [], $this);

        $this->realWorkerNum = $this->workerNum + $this->processNum;
        $processPool = new Pool($this->realWorkerNum);
        $processPool->on('Init', function(InitEventParam $e){
            \Imi\Util\Process::signal(SIGUSR1, function() use($e) {
                $workerIds = [];
                for($i = 0; $i < $this->workerNum; ++$i)
                {
                    $workerIds[] = $i;
                }
                Log::info('Server is reloading all workers now');
                $e->getPool()->restartWorker(...$workerIds);
            });
        });
        $processPool->on('WorkerStart', function (WorkerEventParam $e) {
            $this->workerId = $e->getWorkerId();
            go(function() use($e) {
                if($this->workerId <= $this->workerNum - 1)
                {
                    // 处理请求的 worker 进程
                    $server = ServerManage::createServer($this->name, $this->config);
                    $this->parseServer($server, $this->workerId);
                    Event::trigger('IMI.MAIN_SERVER.WORKER.START', [
                        'server'    => $server,
                        'workerID'  => $this->workerId,
                    ], $this, WorkerStartEventParam::class);
                    $server->getSwooleServer()->start();
                }
                else
                {
                    // 自定义进程
                    ($this->processes[$this->workerId - $this->workerNum])($e->getWorker());
                }
            });
        });
        $processPool->on('WorkerExit', function(WorkerEventParam $e){
            go(function() use($e){
                Event::trigger('IMI.MAIN_SERVER.WORKER.EXIT', [
                    'server'    => $this,
                    'workerID'  => $e->getWorkerId(),
                ], $this, WorkerExitEventParam::class);
            });
        });
        $processPool->on('WorkerStop', function (WorkerEventParam $e) {
            go(function() use($e){
                Event::trigger('IMI.MAIN_SERVER.WORKER.STOP', [
                    'server'    => ServerManage::getServer($this->name),
                    'workerID'  => $e->getWorkerId(),
                ], $this, WorkerStopEventParam::class);
            });
        });
        $processPool->start();
    }

    /**
     * 加载配置
     *
     * @return void
     */
    private function loadConfig()
    {
        if('main' === $this->name)
        {
            $this->config = Config::get('@app.mainServer');
        }
        else
        {
            $this->config = Config::get('@app.subServers.' . $this->name);
        }
        if(!$this->config)
        {
            echo 'Not found server ', $this->name, PHP_EOL;
            return;
        }
        $this->config['coServer'] = true;
    }

    /**
     * 检查端口重用
     *
     * @return void
     */
    private function checkReusePort()
    {
        if($this->config['reuse_port'] ?? false && $this->workerNum > 1 && !Imi::checkReusePort())
        {
            if($this->workerNum > 1)
            {
                throw new \RuntimeException('Your system does not support reuse port! Please use Linux >= 3.9.0, or set worker_num to 1');
            }
        }
    }

    /**
     * 处理服务器对象
     *
     * @param \Imi\Server\Base $server
     * @param int $workerId
     * @return void
     */
    private function parseServer(\Imi\Server\Base $server, $workerId)
    {
        $swooleServer = $server->getSwooleServer();
        $swooleServer->worker_id = $workerId;
        $swooleServer->taskworker = false;
        $swooleServer->master_pid = $this->pid;
        $swooleServer->manager_pid = $this->pid;
        $swooleServer->setting = [
            'worker_num'        =>  $this->workerNum,
            'task_worker_num'   =>  0,
        ];
    }

    /**
     * 获取配置
     *
     * @return void
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 获取服务器名
     *
     * @return void
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 获取工作进程数
     *
     * @return void
     */
    public function getWorkerNum()
    {
        return $this->workerNum;
    }

    /**
     * 获取进程 PID
     *
     * @return void
     */
    public function getPID()
    {
        return $this->pid;
    }

    /**
     * 获取工作进程 ID
     *
     * @return void
     */
    public function getWorkerId()
    {
        return $this->workerId;
    }

    /**
     * 增加一个用户进程
     *
     * @param callable $callable
     * @return void
     */
    public function addProcess(callable $callable)
    {
        ++$this->processNum;
        $this->processes[] = $callable;
    }

}