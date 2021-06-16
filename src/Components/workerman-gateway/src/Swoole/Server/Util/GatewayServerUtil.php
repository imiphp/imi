<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Server\Util;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Contract\ISwooleServerUtil;
use Imi\WorkermanGateway\Server\Util\TGatewayServerUtil;

if (\extension_loaded('swoole'))
{
    /**
     * @Bean("SwooleGatewayServerUtil")
     */
    class GatewayServerUtil implements ISwooleServerUtil
    {
        use TGatewayServerUtil;

        /**
         * 发送消息给 Worker 进程，使用框架内置格式.
         *
         * 返回成功发送消息数量
         *
         * @param int|int[]|null $workerId
         */
        public function sendMessage(string $action, array $data = [], $workerId = null): int
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
        public function sendMessageRaw(string $message, $workerId = null): int
        {
            throw new \RuntimeException('Unsupport operation');
        }

        /**
         * 获取服务器.
         */
        public function getServer(?string $serverName = null): ?ISwooleServer
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
            return ServerManager::getServer($serverName, ISwooleServer::class);
        }
    }
}
