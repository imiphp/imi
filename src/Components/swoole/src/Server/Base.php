<?php

declare(strict_types=1);

namespace Imi\Swoole\Server;

use Imi\App;
use Imi\Event\Event;
use Imi\Server\Contract\BaseServer;
use Imi\Server\Group\Exception\MethodNotFoundException;
use Imi\Server\Group\TServerGroup;
use Imi\Swoole\Process\ProcessManager;
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
use Imi\Util\Imi;
use Imi\Util\Socket\IPEndPoint;
use InvalidArgumentException;
use Swoole\Event as SwooleEvent;
use Swoole\Server;
use Swoole\Server\Port;

abstract class Base extends BaseServer implements ISwooleServer
{
    use TServerGroup;

    /**
     * Swoole 支持的协议列表.
     */
    public const SWOOLE_PROTOCOLS = [
        'open_http_protocol',
        'open_websocket_protocol',
        'open_http2_protocol',
        'open_mqtt_protocol',
        'open_redis_protocol',
    ];

    /**
     * swoole 服务器对象
     *
     * @var \Swoole\Server|\Swoole\Coroutine\Http\Server
     */
    protected $swooleServer;

    /**
     * swoole 监听端口.
     *
     * @var \Swoole\Server\Port|\Swoole\Coroutine\Http\Server
     */
    protected $swoolePort;

    /**
     * 是否为子服务器.
     */
    protected bool $isSubServer = false;

    /**
     * {@inheritDoc}
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

        $configs = $this->config['configs'] ?? [];

        if ($isSubServer)
        {
            $this->swoolePort->set($configs);
        }
        else
        {
            // 强制启用 task 协程化
            $configs['task_enable_coroutine'] = true;
            if (!isset($configs['pid_file']))
            {
                $configs['pid_file'] = Imi::getRuntimePath('swoole.pid');
            }
            if (!isset($configs['log_file']))
            {
                $configs['log_file'] = Imi::getRuntimePath('swoole.log');
            }
            elseif (false === $configs['log_file'])
            {
                // 设为 false 可以禁用 Swoole 错误日志文件
                unset($configs['log_file']);
            }
            $this->swooleServer->set($configs);
        }
        $this->bindEvents();
    }

    /**
     * {@inheritDoc}
     */
    public function getSwooleServer(): Server
    {
        return $this->swooleServer;
    }

    /**
     * {@inheritDoc}
     */
    public function getSwoolePort(): Port
    {
        return $this->swoolePort;
    }

    /**
     * {@inheritDoc}
     */
    public function isSubServer(): bool
    {
        return $this->isSubServer;
    }

    /**
     * {@inheritDoc}
     */
    public function start(): void
    {
        if ($this->isSubServer())
        {
            throw new \RuntimeException('Subserver cannot start, please start the main server');
        }
        ProcessManager::initProcessInfoTable();
        $this->swooleServer->start();
    }

    /**
     * {@inheritDoc}
     */
    public function shutdown(): void
    {
        $this->swooleServer->shutdown();
    }

    /**
     * {@inheritDoc}
     */
    public function reload(): void
    {
        $this->swooleServer->reload();
    }

    /**
     * {@inheritDoc}
     */
    public function callServerMethod(string $methodName, ...$args)
    {
        $server = $this->swooleServer;
        if (!method_exists($server, $methodName))
        {
            throw new MethodNotFoundException(sprintf('%s->%s() method is not exists', \get_class($server), $methodName));
        }

        /** @var \Swoole\WebSocket\Server $server */
        if ('push' === $methodName && !$server->isEstablished($args[0] ?? null))
        {
            return false;
        }

        return $server->$methodName(...$args);
    }

    /**
     * 绑定服务器事件.
     */
    protected function bindEvents(): void
    {
        if (!$this->isSubServer)
        {
            if (\SWOOLE_BASE !== $this->swooleServer->mode)
            {
                $this->swooleServer->on('start', function (Server $server) {
                    try
                    {
                        Event::trigger('IMI.MAIN_SERVER.START', [
                            'server' => $this,
                        ], $this, StartEventParam::class);
                    }
                    catch (\Throwable $ex)
                    {
                        // @phpstan-ignore-next-line
                        App::getBean('ErrorLog')->onException($ex);
                        exit(255);
                    }
                });
            }

            $this->swooleServer->on('shutdown', function (Server $server) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.SHUTDOWN', [
                        'server' => $this,
                    ], $this, ShutdownEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('WorkerStart', function (Server $server, int $workerId) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.WORKER.START', [
                        'server'    => $this,
                        'workerId'  => $workerId,
                    ], $this, WorkerStartEventParam::class);
                    Event::trigger('IMI.SERVER.WORKER_START', [
                        'server'   => $this,
                        'workerId' => $workerId,
                    ], $this);
                }
                catch (\Throwable $ex)
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                    SwooleEvent::exit();
                }
            });

            $this->swooleServer->on('WorkerStop', function (Server $server, int $workerId) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.WORKER.STOP', [
                        'server'    => $this,
                        'workerId'  => $workerId,
                    ], $this, WorkerStopEventParam::class);
                    Event::trigger('IMI.SERVER.WORKER_STOP', [
                        'server'   => $this,
                        'workerId' => $workerId,
                    ], $this);
                }
                catch (\Throwable $ex)
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('WorkerExit', function (Server $server, int $workerId) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.WORKER.EXIT', [
                        'server'    => $this,
                        'workerId'  => $workerId,
                    ], $this, WorkerExitEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('ManagerStart', function (Server $server) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.MANAGER.START', [
                        'server' => $this,
                    ], $this, ManagerStartEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                    exit(255);
                }
            });

            $this->swooleServer->on('ManagerStop', function (Server $server) {
                try
                {
                    Event::trigger('IMI.MAIN_SERVER.MANAGER.STOP', [
                        'server' => $this,
                    ], $this, ManagerStopEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $configs = $this->config['configs'] ?? null;
            if (0 !== ($configs['task_worker_num'] ?? -1))
            {
                $this->swooleServer->on('task', function (Server $server, Server\Task $task) {
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
                        // @phpstan-ignore-next-line
                        App::getBean('ErrorLog')->onException($ex);
                    }
                });
            }

            $this->swooleServer->on('finish', function (Server $server, int $taskId, $data) {
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
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('PipeMessage', function (Server $server, int $workerId, string $message) {
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
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                }
            });

            $this->swooleServer->on('WorkerError', function (Server $server, int $workerId, int $workerPid, int $exitCode, int $signal) {
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
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        $this->__bindEvents();
    }

    /**
     * {@inheritDoc}
     */
    public function getClientAddress($clientId): IPEndPoint
    {
        $clientInfo = $this->swooleServer->getClientInfo($clientId);
        if (false === $clientInfo)
        {
            throw new InvalidArgumentException(sprintf('Client %s does not exists', $clientId));
        }

        return new IPEndPoint($clientInfo['remote_ip'], $clientInfo['remote_port']);
    }

    /**
     * 绑定服务器事件.
     */
    abstract protected function __bindEvents(): void;

    /**
     * 创建 swoole 服务器对象
     */
    abstract protected function createServer(): void;

    /**
     * 从主服务器监听端口，作为子服务器.
     */
    abstract protected function createSubServer(): void;

    /**
     * 获取服务器初始化需要的配置.
     *
     * @return array
     */
    abstract protected function getServerInitConfig();
}
