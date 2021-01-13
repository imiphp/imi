<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Error;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Swoole\Server\TcpServer\IReceiveHandler;
use Imi\Swoole\Server\TcpServer\Message\IReceiveData;

/**
 * TCP 未匹配路由时的处理器.
 *
 * @Bean("TcpRouteNotFoundHandler")
 */
class TcpRouteNotFoundHandler implements ITcpRouteNotFoundHandler
{
    /**
     * 处理器类名，如果为null则使用默认处理.
     *
     * @var string
     */
    protected ?string $handler = null;

    /**
     * 处理方法.
     *
     * @param \Imi\Swoole\Server\TcpServer\Message\IReceiveData $data
     * @param \Imi\Swoole\Server\TcpServer\IReceiveHandler      $handler
     *
     * @return mixed
     */
    public function handle(IReceiveData $data, IReceiveHandler $handler)
    {
        if (null !== $this->handler)
        {
            return App::getBean($this->handler)->handle($data, $handler);
        }
    }
}
