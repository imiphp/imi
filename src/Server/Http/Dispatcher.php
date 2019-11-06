<?php
namespace Imi\Server\Http;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Imi\Server\Http\Message\Response;
use Imi\RequestContext;

/**
 * @Bean("HttpDispatcher")
 */
class Dispatcher
{
    /**
     * 中间件数组
     * @var string[]
     */
    protected $middlewares = [];

    /**
     * 调度
     *
     * @param \Imi\Server\Http\Message\Request $request
     * @return \Imi\Server\Http\Message\Response
     */
    public function dispatch($request): Response
    {
        $requestHandler = new RequestHandler($this->getMiddlewares());
        $response = $requestHandler->handle($request);
        if(!$response->isEnded())
        {
            $response->send();
        }
        return $response;
    }

    protected function getMiddlewares()
    {
        return array_merge($this->middlewares, [
            \Imi\Server\Http\Middleware\ActionWrapMiddleware::class,
        ]);
    }
}