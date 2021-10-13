<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Http\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Swoole\Util\Coroutine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoole\Timer;

/**
 * 当单个请求超过最大执行时间，触发超时处理.
 *
 * @Bean(name="ExecuteTimeoutMiddleware", env="swoole")
 */
class ExecuteTimeoutMiddleware implements MiddlewareInterface
{
    /**
     * 最大执行时间，单位：毫秒.
     *
     * 默认为 30 秒
     */
    protected int $maxExecuteTime = 30000;

    /**
     * 超时处理器.
     */
    protected string $handler = \Imi\Server\Http\Error\ExecuteTimeoutHandler::class;

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $context = RequestContext::getContext();
        $response = $context['response'];
        $server = $context['server'];
        $timerId = Timer::after($this->maxExecuteTime, function () use ($request, $response, $server, $context) {
            RequestContext::muiltiSet((array) $context);
            /** @var \Imi\Server\Http\Error\IExecuteTimeoutHandler $handler */
            $handler = $server->getBean($this->handler);
            // @phpstan-ignore-next-line
            $handler->handle($request, $response);
        });
        Coroutine::defer(function () use ($timerId) {
            Timer::clear($timerId);
        });

        return $handler->handle($request);
    }
}
