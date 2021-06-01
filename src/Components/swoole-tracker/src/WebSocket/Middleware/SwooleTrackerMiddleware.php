<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\WebSocket\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\WebSocket\IMessageHandler;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\WebSocket\Middleware\IMiddleware;
use Imi\SwooleTracker\BaseMiddleware;
use RuntimeException;

/**
 * @Bean("SwooleTrackerWebSocketMiddleware")
 */
class SwooleTrackerMiddleware extends BaseMiddleware implements IMiddleware
{
    /**
     * 获取当前调用方法名称回调.
     *
     * @var callable
     */
    protected $nameHandler;

    public function __init(): void
    {
        if (null === $this->nameHandler)
        {
            throw new RuntimeException('SwooleTrackerWebSocketMiddleware must be set beans: "nameHandler"');
        }
        parent::__init();
    }

    /**
     * @return mixed
     */
    public function process(IFrame $frame, IMessageHandler $handler)
    {
        $funcName = ($this->nameHandler)($frame);
        // @phpstan-ignore-next-line
        $tick = \SwooleTracker\Stats::beforeExecRpc($funcName, $this->serviceName, $this->serverIp);
        try
        {
            $success = $code = null;
            $result = $handler->handle($frame);

            return $result;
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
                if (null === $success)
                {
                    $success = true;
                }
                if (null === $code)
                {
                    $code = RequestContext::get('imi.tracker.code');
                }
                if (null === $code)
                {
                    $code = $success ? $this->successCode : $this->exceptionCode;
                }
                // @phpstan-ignore-next-line
                \SwooleTracker\Stats::afterExecRpc($tick, $success, $code);
            }
        }
    }
}
