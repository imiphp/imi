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
use Imi\Util\Socket\IPEndPoint;

/**
 * @Bean("FpmHttpServer")
 */
class Server extends BaseServer
{
    /**
     * {@inheritDoc}
     */
    public function __construct(string $name, array $config)
    {
        $this->container = App::getContainer();
        $this->name = $name;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getProtocol(): string
    {
        return Protocol::HTTP;
    }

    /**
     * {@inheritDoc}
     */
    public function isLongConnection(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isSSL(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientAddress($clientId): IPEndPoint
    {
        return new IPEndPoint($_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT']);
    }

    /**
     * {@inheritDoc}
     */
    public function start(): void
    {
        try
        {
            $request = new FpmRequest();
            $response = new FpmResponse();
            RequestContext::muiltiSet([
                'server'   => $this,
                'request'  => $request,
                'response' => $response,
            ]);

            // 初始化路由
            /** @var HttpRoute $route */
            $route = $this->getBean('HttpRoute');
            if ($route->isEmpty())
            {
                (new HttpRouteInit())->handle(new EventParam(''));
            }

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
     * {@inheritDoc}
     */
    public function shutdown(): void
    {
        exit;
    }

    /**
     * {@inheritDoc}
     */
    public function reload(): void
    {
    }
}
