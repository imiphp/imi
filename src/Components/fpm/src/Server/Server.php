<?php

declare(strict_types=1);

namespace Imi\Fpm\Server;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Event\EventParam;
use Imi\Fpm\Http\Message\FpmRequest;
use Imi\Fpm\Http\Message\FpmResponse;
use Imi\RequestContext;
use Imi\Server\Contract\BaseServer;
use Imi\Server\Http\Listener\HttpRouteInit;
use Imi\Server\Http\Route\HttpRoute;
use Imi\Server\Protocol;

/**
 * @Bean("FpmHttpServer")
 */
class Server extends BaseServer
{
    /**
     * 构造方法.
     */
    public function __construct(string $name, array $config)
    {
        $this->container = App::getContainer();
        $this->name = $name;
        $this->config = $config;
    }

    /**
     * 获取协议名称.
     */
    public function getProtocol(): string
    {
        return Protocol::HTTP;
    }

    /**
     * 是否为长连接服务
     */
    public function isLongConnection(): bool
    {
        return false;
    }

    /**
     * 是否支持 SSL.
     */
    public function isSSL(): bool
    {
        return false;
    }

    /**
     * 开启服务
     */
    public function start(): void
    {
        try
        {
            // 初始化路由
            /** @var HttpRoute $route */
            $route = $this->getBean('HttpRoute');
            if ($route->isEmpty())
            {
                (new HttpRouteInit())->handle(new EventParam(''));
            }

            $request = new FpmRequest();
            $response = new FpmResponse();
            RequestContext::muiltiSet([
                'server'   => $this,
                'request'  => $request,
                'response' => $response,
            ]);
            /** @var \Imi\Server\Http\Dispatcher $dispatcher */
            $dispatcher = $this->getBean('HttpDispatcher');
            $dispatcher->dispatch($request);
        }
        catch (\Throwable $th)
        {
            if (true !== $this->getBean('HttpErrorHandler')->handle($th))
            {
                App::getBean('ErrorLog')->onException($th);
            }
        }
    }

    /**
     * 终止服务
     */
    public function shutdown(): void
    {
        exit;
    }

    /**
     * 重载服务
     */
    public function reload(): void
    {
    }
}
