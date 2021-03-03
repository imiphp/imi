<?php

declare(strict_types=1);

namespace Imi\Swoole\Server;

use Imi\App;
use Imi\Event\Event;
use Imi\Group\Exception\MethodNotFoundException;
use Imi\Server\Contract\BaseServer;
use Imi\Server\Group\TServerGroup;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Event\Param\FinishEventParam;
use Imi\Swoole\Server\Event\Param\ManagerStartEventParam;
use Imi\Swoole\Server\Event\Param\ManagerStopEventParam;
use Imi\Swoole\Server\Event\Param\PipeMessageEventParam;
use Imi\Swoole\Server\Event\Param\ShutdownEventParam;
use Imi\Swoole\Server\Event\Param\StartEventParam;
use Imi\Swoole\Server\Event\Param\TaskEventParam;
use Imi\Swoole\Server\Event\Param\WorkerErrorEventParam;
use Imi\Swoole\Server\Event\Param\WorkerExitEventParam;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;
use Imi\Swoole\Server\Event\Param\WorkerStopEventParam;
use Swoole\Server;
use Swoole\Server\Port;

abstract class Base extends BaseServer implements ISwooleServer
{
    use TServerGroup;

    /**
     * Swoole 支持的协议列表.
     */
    const SWOOLE_PROTOCOLS = [
        'open_http_protocol',
        'open_websocket_protocol',
        'open_http2_protocol',
        'open_mqtt_protocol',
        'open_redis_protocol',
    ];

    /**
     * swoole 服务器对象
     *
     * @var \Swoole\Server|Swoole\Coroutine\Http\Server
     */
    protected $swooleServer;

    /**
     * swoole 监听端口.
     *
     * @var \Swoole\Server\Port|Swoole\Coroutine\Http\Server
     */
    protected $swoolePort;

    /**
     * 是否为子服务器.
     *
     * @var bool
     */
    protected bool $isSubServer = false;

    /**
     * 构造方法.
     *
     * @param string $name
     * @param array  $config
     * @param bool   $isSubServer 是否为子服务器
     */
    public function __construct(string $name, array $config, bool $isSubServer = false)
    {
        parent::__construct($name, $config);
        $this->isSubServer = $isSubServer;
        if ($isSubServer)
        {
            $this->createSubServer();
        }
        else
        {
            $this->createServer();
            $this->swoolePort = $this->swooleServer->ports[0] ?? $this->swooleServer;
        }

        if (empty($this->config['configs']))
        {
            $configs = [];
        }
        else
        {
            $configs = $this->config['configs'];
        }

        // 强制启用 task 协程化
        $configs['task_enable_coroutine'] = true;

        if ($isSubServer)
        {
            $this->swoolePort->set($configs);
        }
        else
        {
            $this->swooleServer->set($configs);
        }
        $this->bindEvents();
    }

    /**
     * 获取 swoole 服务器对象
     *
     * @return \Swoole\Server
     */
    public function getSwooleServer(): Server
    {
        return $this->swooleServer;
    }

    /**
     * 获取 swoole 监听端口.
     *
     * @return \Swoole\Server\Port
     */
    public function getSwoolePort(): Port
    {
        return $this->swoolePort;
    }

    /**
     * 是否为子服务器.
     *
     * @return bool
     */
    public function isSubServer(): bool
    {
        return $this->isSubServer;
    }

    /**
     * 开启服务
     *
     * @return void
     */
    public function start()
    {
        if ($this->isSubServer())
        {
            throw new \RuntimeException('Subserver cannot start, please start the main server');
        }
        $this->swooleServer->start();
    }

    /**
     * 终止服务
     *
     * @return void
     */
    public function shutdown()
    {
        $this->swooleServer->shutdown();
    }

    /**
     * 重载服务
     *
     * @return void
     */
    public function reload()
    {
        $this->swooleServer->reload();
    }

    /**
     * 调用服务器方法.
     *
     * @param string $methodName
     * @param mixed  ...$args
     *
     * @return mixed
     */
    public function callServerMethod(string $methodName, ...$args)
    {
        $server = $this->swooleServer;
        if (!method_exists($server, $methodName))
        {
            throw new MethodNotFoundException(sprintf('%s->%s() method is not exists', \get_class($server), $methodName));
        }

        if ('push' === $methodName && !$server->isEstablished($args[0] ?? null))
        {
            return false;
        }

        return $server->$methodName(...$args);
    }

    /**
     * 绑定服务器事件.
     *
     * @return void
     */
    protected function bindEvents()
    {
        if (!$this->isSubServer)
        {
            if (\SWOOLE_BASE !== $this->swooleServer->mode)
            {
                $this->swooleServer->on('start', function (\Swoole\Server $server) {
                    try
                    {
                        Event::trigger('IMI.MAIN_SERVER.START', [
                            'server' => $this,
                        ], $this, StartEventParam::class);
                    }
                    catch (\Throwable $ex)
                    {
                        App::getBean('ErrorLog')->onException($ex);
                    }
                });
            }

            $this->swooleServer->on('shutdown', function (\Swoole\Server $server) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.SHUTDOWN', [
                        'server' => $this,
                    ], $this, ShutdownEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('WorkerStart', function (\Swoole\Server $server, int $workerId) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.WORKER.START', [
                        'server'    => $this,
                        'workerId'  => $workerId,
                    ], $this, WorkerStartEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('WorkerStop', function (\Swoole\Server $server, int $workerId) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.WORKER.STOP', [
                        'server'    => $this,
                        'workerId'  => $workerId,
                    ], $this, WorkerStopEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('WorkerExit', function (\Swoole\Server $server, int $workerId) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.WORKER.EXIT', [
                        'server'    => $this,
                        'workerId'  => $workerId,
                    ], $this, WorkerExitEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('ManagerStart', function (\Swoole\Server $server) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.MANAGER.START', [
                        'server' => $this,
                    ], $this, ManagerStartEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('ManagerStop', function (\Swoole\Server $server) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.MANAGER.STOP', [
                        'server' => $this,
                    ], $this, ManagerStopEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $configs = $this->config['configs'] ?? null;
            if (0 !== ($configs['task_worker_num'] ?? -1))
            {
                $this->swooleServer->on('task', function (\Swoole\Server $server, \Swoole\Server\Task $task) {
                    try
                    {
                        Event::trigger('IMI.MAIN_SERVER.TASK', [
                            'server'   => $this,
                            'taskId'   => $task->id,
                            'workerId' => $task->worker_id,
                            'data'     => $task->data,
                            'flags'    => $task->flags,
                            'task'     => $task,
                        ], $this, TaskEventParam::class);
                    }
                    catch (\Throwable $ex)
                    {
                        App::getBean('ErrorLog')->onException($ex);
                    }
                });
            }

            $this->swooleServer->on('finish', function (\Swoole\Server $server, int $taskId, $data) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.FINISH', [
                        'server'    => $this,
                        'taskId'    => $taskId,
                        'data'      => $data,
                    ], $this, FinishEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('PipeMessage', function (\Swoole\Server $server, int $workerId, string $message) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.PIPE_MESSAGE', [
                        'server'    => $this,
                        'workerId'  => $workerId,
                        'message'   => $message,
                    ], $this, PipeMessageEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('WorkerError', function (\Swoole\Server $server, int $workerId, int $workerPid, int $exitCode, int $signal) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.WORKER_ERROR', [
                        'server'    => $this,
                        'workerId'  => $workerId,
                        'workerPid' => $workerPid,
                        'exitCode'  => $exitCode,
                        'signal'    => $signal,
                    ], $this, WorkerErrorEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        $this->__bindEvents();
    }

    /**
     * 绑定服务器事件.
     *
     * @return void
     */
    abstract protected function __bindEvents();

    /**
     * 创建 swoole 服务器对象
     *
     * @return void
     */
    abstract protected function createServer();

    /**
     * 从主服务器监听端口，作为子服务器.
     *
     * @return void
     */
    abstract protected function createSubServer();

    /**
     * 获取服务器初始化需要的配置.
     *
     * @return array
     */
    abstract protected function getServerInitConfig();
}
