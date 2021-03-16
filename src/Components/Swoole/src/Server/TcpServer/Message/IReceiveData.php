<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Message;

interface IReceiveData extends \Imi\Server\TcpServer\Message\IReceiveData
{
    /**
     * 获取Reactor线程ID.
     */
    public function getReactorId(): int;
}
