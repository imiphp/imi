<?php

declare(strict_types=1);

namespace Imi\Swoole\Server;

use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Server\Contract\BaseServer;
use Imi\Server\Event\ServerEvents;
use Imi\Server\Event\WorkerStartEvent;
use Imi\Server\Event\WorkerStopEvent;
use Imi\Server\Group\Exception\MethodNotFoundException;
use Imi\Server\Group\TServerGroup;
use Imi\Server\ServerManager;
use Imi\Swoole\Event\SwooleEvents;
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
use Imi\Worker;
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
     */
    protected \Swoole\Server $swooleServer;

    /**
     * swoole 监听端口.
     */
    protected \Swoole\Server\Port $swoolePort;

    /**
     * {@inheritDoc}
     */
    public function __construct(string $name, array $config,
        /**
         * 是否为子服务器.
         */
        protected bool $isSubServer = false)
    {
        parent::__construct($name, $config);
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
    public function callServerMethod(string $methodName, mixed ...$args): mixed
    {
        $server = $this->swooleServer;
        if (!method_exists($server, $methodName))
        {
            throw new MethodNotFoundException(sprintf('%s->%s() method is not exists', $server::class, $methodName));
        }

        /** @var \Swoole\WebSocket\Server $server */
        if ('push' === $methodName && !$server->isEstablished($args[0] ?? null))
        {
            return false;
        }

        return $server->{$methodName}(...$args);
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
                $this->swooleServer->on('start', function (Server $server): void {
                    try
                    {
                        \Imi\Swoole\Util\Imi::setProcessName('master');
                        Event::dispatch(new StartEventParam($this));
                    }
                    catch (\Throwable $th)
                    {
                        Log::error($th);
                        exit(255);
                    }
                    finally
                    {
                        Log::info('Server start. pid: ' . getmypid());
                    }
                });
            }

            $this->swooleServer->on('shutdown', function (Server $server): void {
                try
                {
                    Event::dispatch(new ShutdownEventParam($this));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
                finally
                {
                    Log::info('Server shutdown. pid: ' . getmypid());
                }
            });

            $this->swooleServer->on('WorkerStart', function (Server $server, int $workerId): void {
                try
                {
                    Event::dispatch(new WorkerStartEventParam(SwooleEvents::SERVER_WORKER_START, $this, $workerId));
                    Event::dispatch(new WorkerStartEvent(ServerEvents::WORKER_START, $this, $workerId));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                    SwooleEvent::exit();
                }
                finally
                {
                    // worker 初始化
                    Worker::inited();
                    Log::info('Worker start #' . Worker::getWorkerId() . '. pid: ' . getmypid());
                }
            });

            $this->swooleServer->on('WorkerStop', function (Server $server, int $workerId): void {
                try
                {
                    Event::dispatch(new WorkerStopEventParam($this, $workerId));
                    Event::dispatch(new WorkerStopEvent($this, $workerId));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
                finally
                {
                    Log::info('Worker stop #' . Worker::getWorkerId() . '. pid: ' . getmypid());
                }
            });

            $this->swooleServer->on('WorkerExit', function (Server $server, int $workerId): void {
                try
                {
                    Event::dispatch(new WorkerExitEventParam($this, $workerId));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
            });

            $this->swooleServer->on('ManagerStart', function (Server $server): void {
                try
                {
                    Event::dispatch(new ManagerStartEventParam($this));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                    exit(255);
                }
                finally
                {
                    Log::info('Manager start. pid: ' . getmypid());
                }
            });

            $this->swooleServer->on('ManagerStop', function (Server $server): void {
                try
                {
                    Event::dispatch(new ManagerStopEventParam($this));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
                finally
                {
                    Log::info('Manager stop. pid: ' . getmypid());
                }
            });

            $configs = $this->config['configs'] ?? null;
            if (0 !== ($configs['task_worker_num'] ?? -1))
            {
                $this->swooleServer->on('task', function (Server $server, Server\Task $task): void {
                    try
                    {
                        Event::dispatch(new TaskEventParam($this, $task->id, $task->worker_id, $task->data, $task->flags, $task));
                    }
                    catch (\Throwable $th)
                    {
                        Log::error($th);
                    }
                });
            }

            $this->swooleServer->on('finish', function (Server $server, int $taskId, $data): void {
                try
                {
                    Event::dispatch(new FinishEventParam($this, $taskId, $data));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
            });

            $this->swooleServer->on('PipeMessage', function (Server $server, int $workerId, string $message): void {
                try
                {
                    Event::dispatch(new PipeMessageEventParam($this, $workerId, $message));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
            });

            $this->swooleServer->on('WorkerError', function (Server $server, int $workerId, int $workerPid, int $exitCode, int $signal): void {
                try
                {
                    Event::dispatch(new WorkerErrorEventParam($this, $workerId, $workerPid, $exitCode, $signal));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
            });
        }
        $this->__bindEvents();
    }

    /**
     * {@inheritDoc}
     */
    public function getClientAddress(string|int $clientId): IPEndPoint
    {
        $clientInfo = $this->swooleServer->getClientInfo($clientId);
        if (false === $clientInfo)
        {
            throw new \InvalidArgumentException(sprintf('Client %s does not exists', $clientId));
        }

        return new IPEndPoint($clientInfo['remote_ip'], $clientInfo['remote_port']);
    }

    /**
     * {@inheritDoc}
     */
    protected function createSubServer(): void
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer();
        $port = $this->swooleServer->addListener($config['host'], (int) $config['port'], (int) $config['sockType']);
        if (false === $port)
        {
            throw new \RuntimeException(sprintf('Swoole addListener(%s, %s, %s) failed', $config['host'], $config['port'], $config['sockType']));
        }
        $this->swoolePort = $port;
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
     * 获取服务器初始化需要的配置.
     */
    abstract protected function getServerInitConfig(): array;
}
