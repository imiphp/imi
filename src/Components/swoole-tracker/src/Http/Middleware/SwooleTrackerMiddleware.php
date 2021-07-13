<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Http\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\SwooleTracker\BaseMiddleware;
use Imi\Util\Http\Consts\StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean("SwooleTrackerHttpMiddleware")
 */
class SwooleTrackerMiddleware extends BaseMiddleware implements MiddlewareInterface
{
    /**
     * 成功的 Http 状态码
     */
    protected int $successStatusCode = StatusCode::OK;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // @phpstan-ignore-next-line
        $tick = \SwooleTracker\Stats::beforeExecRpc($request->getUri()->getPath(), $this->serviceName, $this->serverIp);
        try
        {
            $success = $code = null;
            $response = $handler->handle($request);

            return $response;
        }
        catch (\Throwable $th)
        {
            $success = false;
            $code = $this->exceptionCode;
            throw $th;
        }
        finally
        {
            if ($tick)
            {
                if (null === $success)
                {
                    $success = RequestContext::get('imi.tracker.success');
                }
                if (null === $success && isset($response))
                {
                    $success = $this->successStatusCode === $response->getStatusCode();
                }
                if (null === $code)
                {
                    $code = RequestContext::get('imi.tracker.code');
                }
                if (null === $code && isset($response))
                {
                    $code = $success ? $this->successCode : $response->getStatusCode();
                }
                // @phpstan-ignore-next-line
                \SwooleTracker\Stats::afterExecRpc($tick, $success, $code);
            }
        }
    }
}
