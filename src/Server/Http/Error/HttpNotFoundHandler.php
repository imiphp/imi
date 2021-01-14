<?php

declare(strict_types=1);

namespace Imi\Server\Http\Error;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Util\Http\Consts\StatusCode;
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
    protected string $handler = '';

    public function handle(RequestHandlerInterface $requesthandler, IHttpRequest $request, IHttpResponse $response): IHttpResponse
    {
        if ('' === $this->handler)
        {
            return $response->setStatus(StatusCode::NOT_FOUND);
        }
        else
        {
            return App::getBean($this->handler)->handle($requesthandler, $request, $response);
        }
    }
}
