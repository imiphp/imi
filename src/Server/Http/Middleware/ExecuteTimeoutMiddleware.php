<?php
namespace Imi\Server\Http\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoole\Timer;

/**
 * 当单个请求超过最大执行时间，触发超时处理
 * 
 * @Bean("ExecuteTimeoutMiddleware")
 */
class ExecuteTimeoutMiddleware implements MiddlewareInterface
{
    /**
     * 最大执行时间，单位：毫秒
     * 
     * 默认为 30 秒
     *
     * @var int
     */
    protected $maxExecuteTime = 30000;

    /**
     * 超时处理器
     *
     * @var string
     */
    protected $handler = \Imi\Server\Http\Error\ExecuteTimeoutHandler::class;

    /**
     * 处理方法
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $context = RequestContext::getContext();
        $response = $context['response'];
        $server = $context['server'];
        $timerId = Timer::after($this->maxExecuteTime, function() use($request, $response, $server){
            /** @var \Imi\Server\Http\Message\Request $request */
            RequestContext::muiltiSet([
                'server'    =>  $server,
                'request'   =>  $request,
            ]);
            /** @var \Imi\Server\Http\Error\IExecuteTimeoutHandler $handler */
            $handler = $server->getBean($this->handler);
            $handler->handle($request, $response);
        });
        defer(function() use($timerId){
            Timer::clear($timerId);
        });
        return $handler->handle($request);
    }

}