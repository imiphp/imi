<?php

declare(strict_types=1);

namespace Imi\Workerman\Server;

use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\Contract\BaseServer;
use Imi\Server\Group\Contract\IServerGroup;
use Imi\Server\Group\TServerGroup;
use Imi\Util\Imi;
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
     * 构造方法.
     */
    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);
        $this->worker = $worker = new Worker($this->getWorkerScheme() . '://' . $config['host'] . ':' . $config['port']);
        $worker->name = $name;
        foreach ($config['configs'] as $k => $v)
        {
            $worker->$k = $v;
        }
        $this->bindEvents();
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

            RequestContext::muiltiSet([
                'server' => $this,
                'worker' => $worker,
            ]);

            Event::trigger('IMI.WORKERMAN.SERVER.WORKER_START', [
                'server' => $this,
                'worker' => $worker,
            ], $this);
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
