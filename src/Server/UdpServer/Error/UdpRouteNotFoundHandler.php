<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Error;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * UDP 未匹配路由时的处理器.
 *
 * @Bean("UdpRouteNotFoundHandler")
 */
class UdpRouteNotFoundHandler implements IUdpRouteNotFoundHandler
{
    /**
     * 处理器类名，如果为null则使用默认处理.
     *
     * @var string|null
     */
    protected ?string $handler = null;

    /**
     * 处理方法.
     *
     * @param \Imi\Server\UdpServer\Message\IPacketData $data
     * @param \Imi\Server\UdpServer\IPacketHandler      $handler
     *
     * @return void
     */
    public function handle(IPacketData $data, IPacketHandler $handler)
    {
        if (null !== $this->handler)
        {
            return App::getBean($this->handler)->handle($data, $handler);
        }
    }
}
