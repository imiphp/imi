<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Message;

class ReceiveData extends \Imi\Server\TcpServer\Message\ReceiveData
{
    /**
     * Reactor线程ID.
     */
    protected int $reactorId = 0;

    /**
     * @param int|string $clientId
     */
    public function __construct($clientId, int $reactorId, string $data)
    {
        parent::__construct($clientId, $data);
        $this->reactorId = $reactorId;
    }

    /**
     * 获取Reactor线程ID.
     */
    public function getReactorId(): int
    {
        return $this->reactorId;
    }
}
