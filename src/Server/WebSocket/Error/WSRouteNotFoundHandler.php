<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Error;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Server\WebSocket\IMessageHandler;
use Imi\Server\WebSocket\Message\IFrame;

/**
 * WebSocket 未匹配路由时的处理器.
 *
 * @Bean("WSRouteNotFoundHandler")
 */
class WSRouteNotFoundHandler implements IWSRouteNotFoundHandler
{
    /**
     * 处理器类名，如果为null则使用默认处理.
     */
    protected ?string $handler = null;

    /**
     * 处理方法.
     *
     * @return mixed
     */
    public function handle(IFrame $frame, IMessageHandler $handler)
    {
        if (null !== $this->handler)
        {
            return App::getBean($this->handler)->handle($frame, $handler);
        }
    }
}
