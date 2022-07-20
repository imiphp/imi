<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Server\Util;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Server;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Contract\ISwooleServerUtil;
use Imi\WorkermanGateway\Server\Util\TGatewayServerUtil;

if (\Imi\Util\Imi::checkAppType('swoole'))
{
    /**
     * @Bean("SwooleGatewayServerUtil")
     */
    class GatewayServerUtil implements ISwooleServerUtil
    {
        use TGatewayServerUtil;

        /**
         * {@inheritDoc}
         */
        public function sendMessage(string $action, array $data = [], $workerId = null): int
        {
            throw new \RuntimeException('Unsupport operation');
        }

        /**
         * {@inheritDoc}
         */
        public function sendMessageRaw(string $message, $workerId = null): int
        {
            throw new \RuntimeException('Unsupport operation');
        }

        /**
         * {@inheritDoc}
         */
        public function getServer(?string $serverName = null): ?ISwooleServer
        {
            return Server::getServer($serverName, ISwooleServer::class);
        }
    }
}
