<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Server\Util;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\ServerManager;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Imi\Workerman\Server\Contract\IWorkermanServerUtil;
use Imi\WorkermanGateway\Server\Util\TGatewayServerUtil;

/**
 * @Bean("WorkermanGatewayServerUtil")
 */
class GatewayServerUtil implements IWorkermanServerUtil
{
    use TGatewayServerUtil;

    /**
     * 发送消息给 Worker 进程，使用框架内置格式.
     *
     * 返回成功发送消息数量
     *
     * @param int|int[]|null $workerId
     */
    public function sendMessage(string $action, array $data = [], $workerId = null, ?string $serverName = null): int
    {
        throw new \RuntimeException('Unsupport operation');
    }

    /**
     * 发送消息给 Worker 进程.
     *
     * 返回成功发送消息数量
     *
     * @param int|int[]|null $workerId
     */
    public function sendMessageRaw(array $data, $workerId = null, ?string $serverName = null): int
    {
        throw new \RuntimeException('Unsupport operation');
    }

    /**
     * 获取服务器.
     */
    public function getServer(?string $serverName = null): ?IWorkermanServer
    {
        if (null === $serverName)
        {
            $server = RequestContext::getServer();
            if ($server)
            {
                // @phpstan-ignore-next-line
                return $server;
            }
            $serverName = 'main';
        }

        // @phpstan-ignore-next-line
        return ServerManager::getServer($serverName, IWorkermanServer::class);
    }
}
