<?php

declare(strict_types=1);

namespace Imi\Workerman\Server;

use Channel\Client;
use GatewayWorker\Lib\Gateway;
use Imi\App;
use Imi\Config;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\Log\Handler\ConsoleHandler;
use Imi\Log\Log;
use Imi\Log\Logger;
use Imi\RequestContext;
use Imi\Server\Contract\BaseServer;
use Imi\Server\Event\PipeMessageEvent;
use Imi\Server\Event\ServerEvents;
use Imi\Server\Event\WorkerStartEvent;
use Imi\Server\Event\WorkerStopEvent;
use Imi\Server\Group\Contract\IServerGroup;
use Imi\Server\Group\TServerGroup;
use Imi\Server\ServerManager;
use Imi\Util\Imi;
use Imi\Util\Socket\IPEndPoint;
use Imi\Worker as ImiWorker;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Imi\Workerman\Server\Event\ConnectEvent;
use Imi\Workerman\Server\Event\ServerBufferDrainEvent;
use Imi\Workerman\Server\Event\ServerBufferFullEvent;
use Imi\Workerman\Server\Event\WorkerReloadEvent;
use Imi\Workerman\Server\Http\Event\WorkermanConnectionCloseEvent;
use Imi\Workerman\Server\Http\Event\WorkermanErrorEvent;
use Symfony\Component\Console\Output\StreamOutput;
use Workerman\Connection\ConnectionInterface;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

abstract class Base extends BaseServer implements IWorkermanServer, IServerGroup
{
    use TServerGroup;

    /**
     * Workerman Worker 对象
     */
    protected Worker $worker;

    /**
     * Workerman Worker 类名.
     */
    protected string $workerClass = WorkermanServerWorker::class;

    /**
     * {@inheritDoc}
     */
    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);
        $this->worker = $this->createServer();
        $this->bindEvents();
    }

    /**
     * {@inheritDoc}
     */
    public function parseConfig(array &$config): void
    {
        if (!isset($config['worker']))
        {
            $config['worker'] = $this->workerClass;
        }
        if (!isset($config['socketName']))
        {
            if (isset($config['host'], $config['port']))
            {
                $config['socketName'] = $this->getWorkerScheme() . '://' . $config['host'] . ':' . $config['port'];
            }
            else
            {
                $config['socketName'] = '';
            }
        }
    }

    protected function createServer(): Worker
    {
        $config = $this->config;
        $this->parseConfig($config);

        $class = $this->workerClass = $config['worker'];
        $worker = new $class($config['socketName'], $config['context'] ?? []);
        $worker->name = $this->name;
        foreach ($config['configs'] ?? [] as $k => $v)
        {
            $worker->{$k} = $v;
        }

        return $worker;
    }

    /**
     * {@inheritDoc}
     */
    public function isSSL(): bool
    {
        return isset($this->worker) && 'ssl' === $this->worker->transport;
    }

    /**
     * {@inheritDoc}
     */
    public function getWorker(): Worker
    {
        return $this->worker;
    }

    /**
     * {@inheritDoc}
     */
    public function start(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function shutdown(): void
    {
        $this->workerClass::stopAll();
    }

    /**
     * {@inheritDoc}
     */
    public function reload(): void
    {
        $this->workerClass::reloadAllWorkers();
    }

    /**
     * 绑定服务器事件.
     */
    protected function bindEvents(): void
    {
        $this->worker->onBufferDrain = function (ConnectionInterface $connection): void {
            try
            {
                // @phpstan-ignore-next-line
                $clientId = $connection->id;
                RequestContext::muiltiSet([
                    'server'   => $this,
                    'clientId' => $clientId,
                ]);
                Event::dispatch(new ServerBufferDrainEvent($this, $clientId, $connection));
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
        };

        $this->worker->onBufferFull = function (ConnectionInterface $connection): void {
            try
            {
                // @phpstan-ignore-next-line
                $clientId = $connection->id;
                RequestContext::muiltiSet([
                    'server'   => $this,
                    'clientId' => $clientId,
                ]);
                Event::dispatch(new ServerBufferFullEvent($this, $clientId, $connection));
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
        };

        $this->worker->onClose = function (ConnectionInterface $connection): void {
            try
            {
                // @phpstan-ignore-next-line
                $clientId = $connection->id;
                RequestContext::muiltiSet([
                    'server'   => $this,
                    'clientId' => $clientId,
                ]);
                Event::dispatch(new WorkermanConnectionCloseEvent($this, $clientId, $connection));
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
        };

        $this->worker->onConnect = function (ConnectionInterface $connection): void {
            try
            {
                // @phpstan-ignore-next-line
                $clientId = $connection->id;
                RequestContext::muiltiSet([
                    'server'   => $this,
                    'clientId' => $clientId,
                ]);
                ConnectionContext::create();
                Event::dispatch(new ConnectEvent($this, $clientId, $connection));
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
        };

        $this->worker->onError = function (ConnectionInterface $connection, int $code, string $msg): void {
            try
            {
                // @phpstan-ignore-next-line
                $clientId = $connection->id;
                RequestContext::muiltiSet([
                    'server'   => $this,
                    'clientId' => $clientId,
                ]);
                Event::dispatch(new WorkermanErrorEvent($this, $clientId, $connection, $code, $msg));
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
        };

        $this->worker->onWorkerReload = function (Worker $worker): void {
            try
            {
                RequestContext::muiltiSet([
                    'server' => $this,
                    'worker' => $worker,
                ]);
                Event::dispatch(new WorkerReloadEvent($this, $worker));
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
        };

        $this->worker->onWorkerStart = function (Worker $worker): void {
            try
            {
                // 随机数播种
                mt_srand();

                $workerClass = $this->workerClass;
                if ($workerClass::$daemonize)
                {
                    /** @var Logger $loggerInstance */
                    $loggerInstance = App::getBean('Logger');
                    foreach ($loggerInstance->getLoggers(true) as $logger)
                    {
                        foreach ($logger->getHandlers() as $handler)
                        {
                            if ($handler instanceof ConsoleHandler)
                            {
                                $handler->setOutput($stdoutStream ??= new StreamOutput(fopen($workerClass::$stdoutFile, 'a')));
                            }
                        }
                    }
                }

                Imi::loadRuntimeInfo(Imi::getCurrentModeRuntimePath('runtime'));

                // 创建共享 Worker 的服务
                $config = $this->config;
                if (!($config['shareWorker'] ?? false) && ($config['autorun'] ?? true))
                {
                    foreach (Config::get('@app.workermanServer') as $name => $config)
                    {
                        $shareWorker = $config['shareWorker'] ?? false;
                        if (false !== $shareWorker && $this->getName() === $shareWorker)
                        {
                            /** @var IWorkermanServer $server */
                            $server = ServerManager::createServer($name, $config);
                            $subWorker = $server->getWorker();
                            $subWorker->count = $worker->count;
                            // @phpstan-ignore-next-line
                            $subWorker->onWorkerStop = null;
                            $subWorker->listen();
                        }
                    }
                }

                RequestContext::muiltiSet([
                    'server' => $this,
                    'worker' => $worker,
                ]);

                // 多进程通讯组件连接
                $channel = Config::get('@app.workerman.channel');
                if ($channel)
                {
                    Client::connect($channel['host'] ?: '127.0.0.1', $channel['port'] ?: 2206);
                    // 监听进程通讯
                    $callback = static function (array $data): void {
                        $action = $data['action'] ?? null;
                        if (!$action)
                        {
                            return;
                        }
                        Event::dispatch(new PipeMessageEvent('imi.pipe_message.' . $action, [
                            'data' => $data,
                        ]));
                    };
                    $workerId = ImiWorker::getWorkerId();
                    Client::on('imi.process.message.' . $this->getName() . '.' . $workerId, $callback);
                    Client::on('imi.process.message.' . $workerId, $callback);
                }

                if (isset($config['configs']['registerAddress']) && class_exists(Gateway::class))
                {
                    Gateway::$registerAddress = $config['configs']['registerAddress'];
                    // @phpstan-ignore-next-line
                    if (isset(Gateway::$persistentConnection))
                    {
                        Gateway::$persistentConnection = false;
                    }
                }

                Event::dispatch(new \Imi\Workerman\Server\Event\WorkerStartEvent($this, $worker));
                Event::dispatch(new WorkerStartEvent(ServerEvents::WORKER_START, $this, $worker->id));

                \Imi\Worker::inited();
                foreach (ServerManager::getServers() as $name => $_)
                {
                    Server::getInstance($name);
                    if ($channel)
                    {
                        Client::on('imi.process.message.' . $name . '.' . $workerId, $callback);
                    }
                }
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
            finally
            {
                Log::info('Worker start #' . $worker->id . '. pid: ' . getmypid());
            }
        };

        $this->worker->onWorkerStop = function (Worker $worker): void {
            try
            {
                RequestContext::muiltiSet([
                    'server' => $this,
                    'worker' => $worker,
                ]);
                Event::dispatch(new \Imi\Workerman\Server\Event\WorkerStopEvent($this, $worker));
                Event::dispatch(new WorkerStopEvent($this, $worker->id));
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
            finally
            {
                Log::info('Worker stop #' . $worker->id . '. pid: ' . getmypid());
            }
        };
    }

    /**
     * {@inheritDoc}
     */
    public function getClientAddress(string|int $clientId): IPEndPoint
    {
        /** @var TcpConnection|null $connection */
        $connection = $this->worker->connections[$clientId] ?? null;
        if (null === $connection)
        {
            throw new \InvalidArgumentException(sprintf('Client %s does not exists', $clientId));
        }

        return new IPEndPoint($connection->getRemoteIp(), $connection->getRemotePort());
    }

    /**
     * 获取实例化 Worker 用的协议.
     */
    abstract protected function getWorkerScheme(): string;
}
