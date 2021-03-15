<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http;

use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\Protocol;
use Imi\Util\ImiPriority;
use Imi\Workerman\Http\Message\WorkermanRequest;
use Imi\Workerman\Http\Message\WorkermanResponse;
use Imi\Workerman\Server\Base;
use Imi\Workerman\Server\Http\Listener\BeforeRequest;
use Imi\Workerman\Server\Protocol\WorkermanHttp;
use Workerman\Connection\ConnectionInterface;
use Workerman\Protocols\Http\Response;

/**
 * @Bean("WorkermanHttpServer")
 */
class Server extends Base
{
    /**
     * 构造方法.
     *
     * @param string $name
     * @param array  $config
     */
    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);
        $this->worker->protocol = WorkermanHttp::class;
    }

    /**
     * 获取协议名称.
     *
     * @return string
     */
    public function getProtocol(): string
    {
        return Protocol::HTTP;
    }

    /**
     * 是否为长连接服务
     *
     * @return bool
     */
    public function isLongConnection(): bool
    {
        return false;
    }

    /**
     * 绑定服务器事件.
     *
     * @return void
     */
    protected function bindEvents(): void
    {
        parent::bindEvents();
        Event::on('IMI.WORKERMAN.SERVER.MESSAGE', [new BeforeRequest($this), 'handle'], ImiPriority::IMI_MAX);
        $this->worker->onMessage = function (ConnectionInterface $connection, $data) {
            $worker = $this->worker;
            // @phpstan-ignore-next-line
            $request = new WorkermanRequest($worker, $connection, $data);
            // @phpstan-ignore-next-line
            $response = new WorkermanResponse($worker, $connection, new Response());
            RequestContext::muiltiSet([
                'server'   => $this,
                'request'  => $request,
                'response' => $response,
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.MESSAGE', [
                'server'   => $this,
                'request'  => $request,
                'response' => $response,
            ], $this);
        };
    }

    /**
     * 获取实例化 Worker 用的协议.
     *
     * @return string
     */
    protected function getWorkerScheme(): string
    {
        return 'http';
    }
}
