<?php

declare(strict_types=1);

namespace Imi\Workerman\Server;

use Channel\Client;
use Imi\Config;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\Contract\BaseServer;
use Imi\Server\Group\Contract\IServerGroup;
use Imi\Server\Group\TServerGroup;
use Imi\Server\ServerManager;
use Imi\Util\Imi;
use Imi\Worker as ImiWorker;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Workerman\Connection\ConnectionInterface;
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
    protected string $workerClass = Worker::class;

    /**
     * 构造方法.
     */
    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);
        $this->worker = $this->createServer();
        $this->bindEvents();
    }

    protected function createServer(): Worker
    {
        $config = $this->config;
        if (isset($config['worker']))
        {
            $class = $this->workerClass = $config['worker'];
        }
        else
        {
            $class = $this->workerClass;
        }
        if (isset($config['socketName']))
        {
            $socketName = $config['socketName'];
        }
        else
        {
            $socketName = $this->getWorkerScheme() . '://' . $config['host'] . ':' . $config['port'];
        }
        $worker = new $class($socketName);
        $worker->name = $this->name;
        foreach ($config['configs'] ?? [] as $k => $v)
        {
            $worker->$k = $v;
        }

        return $worker;
    }

    /**
     * 是否支持 SSL.
     */
    public function isSSL(): bool
    {
        return isset($this->worker) && 'ssl' === $this->worker->transport;
    }

    /**
     * 获取 Workerman Worker 对象
     */
    public function getWorker(): Worker
    {
        return $this->worker;
    }

    /**
     * 开启服务
     */
    public function start(): void
    {
    }

    /**
     * 终止服务
     */
    public function shutdown(): void
    {
        Worker::stopAll();
    }

    /**
     * 重载服务
     */
    public function reload(): void
    {
        Worker::reloadAllWorkers();
    }

    /**
     * 绑定服务器事件.
     */
    protected function bindEvents(): void
    {
        $this->worker->onBufferDrain = function (ConnectionInterface $connection) {
            RequestContext::muiltiSet([
                'server' => $this,
            ]);
            $this->trigger('IMI.WORKERMAN.SERVER.BUFFER_DRAIN', [
                'server'     => $this,
                'connection' => $connection,
            ], $this);
        };

        $this->worker->onBufferFull = function (ConnectionInterface $connection) {
            RequestContext::muiltiSet([
                'server' => $this,
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.BUFFER_FULL', [
                'server'     => $this,
                'connection' => $connection,
            ], $this);
        };

        $this->worker->onClose = function (ConnectionInterface $connection) {
            RequestContext::muiltiSet([
                'server' => $this,
                // @phpstan-ignore-next-line
                'fd'     => $connection->id,
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.CLOSE', [
                'server'     => $this,
                'connection' => $connection,
            ], $this);
        };

        $this->worker->onConnect = function (ConnectionInterface $connection) {
            RequestContext::muiltiSet([
                'server' => $this,
                // @phpstan-ignore-next-line
                'fd'     => $connection->id,
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.CONNECT', [
                'server'     => $this,
                'connection' => $connection,
            ], $this);
        };

        $this->worker->onError = function (ConnectionInterface $connection, int $code, string $msg) {
            RequestContext::muiltiSet([
                'server' => $this,
                // @phpstan-ignore-next-line
                'fd'     => $connection->id,
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.ERROR', [
                'server'     => $this,
                'connection' => $connection,
                'code'       => $code,
                'msg'        => $msg,
            ], $this);
        };

        $this->worker->onWorkerReload = function (Worker $worker) {
            RequestContext::muiltiSet([
                'server' => $this,
                'worker' => $worker,
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.WORKER_RELOAD', [
                'server' => $this,
                'worker' => $worker,
            ], $this);
        };

        $this->worker->onWorkerStart = function (Worker $worker) {
            // 随机数播种
            mt_srand();

            Imi::loadRuntimeInfo(Imi::getRuntimePath('runtime'));

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
                $callback = function (array $data) {
                    $action = $data['action'] ?? null;
                    if (!$action)
                    {
                        return;
                    }
                    Event::trigger('IMI.PIPE_MESSAGE.' . $action, [
                        'data'      => $data,
                    ]);
                };
                $workerId = ImiWorker::getWorkerId();
                Client::on('imi.process.message.' . $this->getName() . '.' . $workerId, $callback);
                Client::on('imi.process.message.' . $workerId, $callback);
            }

            Event::trigger('IMI.WORKERMAN.SERVER.WORKER_START', [
                'server' => $this,
                'worker' => $worker,
            ], $this);

            \Imi\Worker::inited();
            Server::getInstance();
        };

        $this->worker->onWorkerStop = function (Worker $worker) {
            RequestContext::muiltiSet([
                'server' => $this,
                'worker' => $worker,
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.WORKER_STOP', [
                'server' => $this,
                'worker' => $worker,
            ], $this);
        };
    }

    /**
     * 获取实例化 Worker 用的协议.
     */
    abstract protected function getWorkerScheme(): string;
}
