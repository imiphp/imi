<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Server\Util;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Server;
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
     * {@inheritDoc}
     */
    public function sendMessage(string $action, array $data = [], $workerId = null, ?string $serverName = null): int
    {
        throw new \RuntimeException('Unsupport operation');
    }

    /**
     * {@inheritDoc}
     */
    public function sendMessageRaw(array $data, $workerId = null, ?string $serverName = null): int
    {
        throw new \RuntimeException('Unsupport operation');
    }

    /**
     * {@inheritDoc}
     */
    public function getServer(?string $serverName = null): ?IWorkermanServer
    {
        // @phpstan-ignore-next-line
        return Server::getServer($serverName, IWorkermanServer::class);
    }
}
