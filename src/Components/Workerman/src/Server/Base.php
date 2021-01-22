<?php

declare(strict_types=1);

namespace Imi\Workerman\Server;

use Imi\Event\Event;
use Imi\Server\Contract\BaseServer;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Workerman\Connection\ConnectionInterface;
use Workerman\Worker;

abstract class Base extends BaseServer implements IWorkermanServer
{
    /**
     * Workerman Worker 对象
     *
     * @var Worker
     */
    protected Worker $worker;

    /**
     * 构造方法.
     *
     * @param string $name
     * @param array  $config
     */
    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);
        $this->worker = $worker = new Worker('http://' . $config['host'] . ':' . $config['port']);
        foreach ($config['configs'] as $k => $v)
        {
            $worker->$k = $v;
        }
        $this->bindEvents();
    }

    /**
     * 是否支持 SSL.
     *
     * @return bool
     */
    public function isSSL(): bool
    {
        return isset($this->worker) && 'ssl' === $this->worker->transport;
    }

    /**
     * 获取 Workerman Worker 对象
     *
     * @return \Workerman\Worker
     */
    public function getWorker(): Worker
    {
        return $this->worker;
    }

    /**
     * 开启服务
     *
     * @return void
     */
    public function start()
    {
    }

    /**
     * 绑定服务器事件.
     *
     * @return void
     */
    protected function bindEvents()
    {
        $this->worker->onBufferDrain = function (ConnectionInterface $connection) {
            Event::trigger('IMI.WORKERMAN.SERVER.BUFFER_DRAIN', [
                'server'     => $this,
                'connection' => $connection,
            ], $this);
        };

        $this->worker->onBufferFull = function (ConnectionInterface $connection) {
            Event::trigger('IMI.WORKERMAN.SERVER.BUFFER_FULL', [
                'server'     => $this,
                'connection' => $connection,
            ], $this);
        };

        $this->worker->onClose = function (ConnectionInterface $connection) {
            Event::trigger('IMI.WORKERMAN.SERVER.CLOSE', [
                'server'     => $this,
                'connection' => $connection,
            ], $this);
        };

        $this->worker->onConnect = function (ConnectionInterface $connection) {
            Event::trigger('IMI.WORKERMAN.SERVER.CONNECT', [
                'server'     => $this,
                'connection' => $connection,
            ], $this);
        };

        $this->worker->onError = function (ConnectionInterface $connection, int $code, string $msg) {
            Event::trigger('IMI.WORKERMAN.SERVER.ERROR', [
                'server'     => $this,
                'connection' => $connection,
                'code'       => $code,
                'msg'        => $msg,
            ], $this);
        };

        $this->worker->onWorkerReload = function (Worker $worker) {
            Event::trigger('IMI.WORKERMAN.SERVER.WORKER_RELOAD', [
                'server' => $this,
                'worker' => $worker,
            ], $this);
        };

        $this->worker->onWorkerStart = function (Worker $worker) {
            Event::trigger('IMI.WORKERMAN.SERVER.WORKER_START', [
                'server' => $this,
                'worker' => $worker,
            ], $this);
        };

        $this->worker->onWorkerStop = function (Worker $worker) {
            Event::trigger('IMI.WORKERMAN.SERVER.WORKER_STOP', [
                'server' => $this,
                'worker' => $worker,
            ], $this);
        };
    }
}
