<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\UDP\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;
use Imi\Server\UdpServer\Middleware\IMiddleware;
use Imi\SwooleTracker\BaseMiddleware;
use RuntimeException;

/**
 * @Bean("SwooleTrackerUDPMiddleware")
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
            throw new RuntimeException('SwooleTrackerTCPMiddleware must be set beans: "nameHandler"');
        }
        parent::__init();
    }

    /**
     * {@inheritDoc}
     */
    public function process(IPacketData $data, IPacketHandler $handler)
    {
        $funcName = ($this->nameHandler)($data);
        // @phpstan-ignore-next-line
        $tick = \SwooleTracker\Stats::beforeExecRpc($funcName, $this->serviceName, $this->serverIp);
        try
        {
            $success = $code = null;
            $result = $handler->handle($data);

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
