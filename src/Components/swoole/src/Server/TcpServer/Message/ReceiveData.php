<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Message;

class ReceiveData extends \Imi\Server\TcpServer\Message\ReceiveData
{
    public function __construct(int|string $clientId,
        /**
         * Reactor线程ID.
         */
        protected int $reactorId, string $data)
    {
        parent::__construct($clientId, $data);
    }

    /**
     * 获取Reactor线程ID.
     */
    public function getReactorId(): int
    {
        return $this->reactorId;
    }
}
