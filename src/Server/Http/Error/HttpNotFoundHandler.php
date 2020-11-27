<?php

namespace Imi\Server\Http\Error;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Util\Http\Consts\StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * http 未匹配路由时的处理器.
 *
 * @Bean("HttpNotFoundHandler")
 */
class HttpNotFoundHandler implements IHttpNotFoundHandler
{
    /**
     * 处理器类名，如果为null则使用默认处理.
     *
     * @var string
     */
    protected string $handler;

    public function handle(RequestHandlerInterface $requesthandler, ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!isset($this->handler))
        {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }
        else
        {
            return App::getBean($this->handler)->handle($requesthandler, $request, $response);
        }
    }
}
