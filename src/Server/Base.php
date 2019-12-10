<?php
namespace Imi\Server;

use Imi\App;
use Imi\Event\Event;
use Imi\Event\TEvent;
use Imi\Bean\Container;
use Imi\Server\Annotation\Listen;
use Imi\Server\Group\TServerGroup;
use Imi\Server\Event\Param\TaskEventParam;
use Imi\Server\Event\Param\StartEventParam;
use Imi\Server\Event\Param\FinishEventParam;
use Imi\Server\Event\Param\ShutdownEventParam;
use Imi\Server\Event\Param\WorkerStopEventParam;
use Imi\Server\Event\Param\ManagerStopEventParam;
use Imi\Server\Event\Param\PipeMessageEventParam;
use Imi\Server\Event\Param\WorkerErrorEventParam;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Event\Param\ManagerStartEventParam;
use Imi\Server\Event\Param\WorkerExitEventParam;

abstract class Base
{
    use TEvent, TServerGroup;

    /**
     * swoole 服务器对象
     * @var \Swoole\Server
     */
    protected $swooleServer;

    /**
     * swoole 监听端口
     *
     * @var \Swoole\Server\Port
     */
    protected $swoolePort;

    /**
     * 服务器配置
     * @var array
     */
    protected $config;

    /**
     * 是否为子服务器
     * @var bool
     */
    protected $isSubServer;

    /**
     * 服务器名称
     * @var string
     */
    protected $name;

    /**
     * 容器
     * @var \Imi\Bean\Container
     */
    protected $container;

    /**
     * 构造方法
     * @param string $name
     * @param array $config
     * @param \Swoole\Server $serverInstance
     * @param bool $subServer 是否为子服务器
     */
    public function __construct($name, $config, $isSubServer = false)
    {
        $this->container = App::getContainer()->newSubContainer();
        $this->name = $name;
        $this->config = $config;
        $this->isSubServer = $isSubServer;
        if($isSubServer)
        {
            $this->createSubServer();
        }
        else
        {
            $this->createServer();
        }
        if(!empty($this->config['configs']))
        {
            ($this->swoolePort ?? $this->swooleServer)->set($this->config['configs']);
        }
        $this->bindEvents();
    }

    /**
     * 获取 swoole 服务器对象
     * @return \Swoole\Server
     */
    public function getSwooleServer()
    {
        return $this->swooleServer;
    }

    /**
     * 获取 swoole 监听端口
     *
     * @return \Swoole\Server\Port
     */
    public function getSwoolePort()
    {
        return $this->swoolePort;
    }

    /**
     * 是否为子服务器
     * @return boolean
     */
    public function isSubServer()
    {
        return $this->isSubServer;
    }

    /**
     * 绑定服务器事件
     * @return void
     */
    protected function bindEvents()
    {
        if(!$this->isSubServer)
        {
            $this->swooleServer->on('start', function(\Swoole\Server $server){
                try{
                    Event::trigger('IMI.MAIN_SERVER.START', [
                        'server' => $this,
                    ], $this, StartEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('shutdown', function(\Swoole\Server $server){
                try{
                    Event::trigger('IMI.MAIN_SERVER.SHUTDOWN', [
                        'server' => $this,
                    ], $this, ShutdownEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('WorkerStart', function(\Swoole\Server $server, int $workerID){
                try{
                    Event::trigger('IMI.MAIN_SERVER.WORKER.START', [
                        'server'    => $this,
                        'workerID'  => $workerID,
                    ], $this, WorkerStartEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('WorkerStop', function(\Swoole\Server $server, int $workerID){
                try{
                    Event::trigger('IMI.MAIN_SERVER.WORKER.STOP', [
                        'server'    => $this,
                        'workerID'  => $workerID,
                    ], $this, WorkerStopEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('WorkerExit', function(\Swoole\Server $server, int $workerID){
                try{
                    Event::trigger('IMI.MAIN_SERVER.WORKER.EXIT', [
                        'server'    => $this,
                        'workerID'  => $workerID,
                    ], $this, WorkerExitEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('ManagerStart', function(\Swoole\Server $server){
                try{
                    Event::trigger('IMI.MAIN_SERVER.MANAGER.START', [
                        'server' => $this,
                    ], $this, ManagerStartEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('ManagerStop', function(\Swoole\Server $server){
                try{
                    Event::trigger('IMI.MAIN_SERVER.MANAGER.STOP', [
                        'server' => $this,
                    ], $this, ManagerStopEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            if(0 !== ($this->config['configs']['task_worker_num'] ?? -1))
            {
                if(
                    (!isset($this->config['configs']['enable_coroutine']) || $this->config['configs']['enable_coroutine'])
                    && isset($this->config['configs']['task_enable_coroutine']) && $this->config['configs']['task_enable_coroutine'])
                {
                    $this->swooleServer->on('task', function(\Swoole\Server $server, \Swoole\Server\Task $task){
                        try{
                            Event::trigger('IMI.MAIN_SERVER.TASK', [
                                'server'   => $this,
                                'taskID'   => $task->id,
                                'workerID' => $task->worker_id,
                                'data'     => $task->data,
                                'flags'    => $task->flags,
                                'task'     => $task,
                            ], $this, TaskEventParam::class);
                        }
                        catch(\Throwable $ex)
                        {
                            App::getBean('ErrorLog')->onException($ex);
                        }
                    });
                }
                else
                {
                    $this->swooleServer->on('task', function(\Swoole\Server $server, int $taskID, int $workerID, $data) {
                        try
                        {
                            Event::trigger('IMI.MAIN_SERVER.TASK', [
                                'server'   => $this,
                                'taskID'   => $taskID,
                                'workerID' => $workerID,
                                'data'     => $data,
                            ], $this, TaskEventParam::class);
                        }
                        catch (\Throwable $ex)
                        {
                            App::getBean('ErrorLog')->onException($ex);
                        }
                    });
                }
            }

            $this->swooleServer->on('finish', function(\Swoole\Server $server, int $taskID, $data){
                try{
                    Event::trigger('IMI.MAIN_SERVER.FINISH', [
                        'server'    => $this,
                        'taskID'    => $taskID,
                        'data'      => $data,
                    ], $this, FinishEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('PipeMessage', function(\Swoole\Server $server, int $workerID, $message){
                try{
                    Event::trigger('IMI.MAIN_SERVER.PIPE_MESSAGE', [
                        'server'    => $this,
                        'workerID'  => $workerID,
                        'message'   => $message,
                    ], $this, PipeMessageEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('WorkerError', function(\Swoole\Server $server, int $workerID, int $workerPid, int $exitCode, int $signal){
                try{
                    Event::trigger('IMI.MAIN_SERVER.WORKER_ERROR', [
                        'server'    => $this,
                        'workerID'  => $workerID,
                        'workerPid' => $workerPid,
                        'exitCode'  => $exitCode,
                        'signal'    => $signal,
                    ], $this, WorkerErrorEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        $this->__bindEvents();
    }

    /**
     * 获取配置信息
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 获取服务器名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 获取容器对象
     * @return \Imi\Bean\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * 获取Bean对象
     * @param string $name
     * @return mixed
     */
    public function getBean($name, ...$params)
    {
        return $this->container->get($name, ...$params);
    }

    /**
     * 绑定服务器事件
     * @return void
     */
    protected abstract function __bindEvents();

    /**
     * 创建 swoole 服务器对象
     * @return void
     */
    protected abstract function createServer();

    /**
     * 从主服务器监听端口，作为子服务器
     * @return void
     */
    protected abstract function createSubServer();

    /**
     * 获取服务器初始化需要的配置
     * @return array
     */
    protected abstract function getServerInitConfig();
}