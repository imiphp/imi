<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Message;

class ReceiveData extends \Imi\Server\TcpServer\Message\ReceiveData
{
    /**
     * Reactor线程ID.
     *
     * @var int
     */
    protected int $reactorId = 0;

    public function __construct(int $fd, int $reactorId, string $data)
    {
        parent::__construct($fd, $data);
        $this->reactorId = $reactorId;
    }

    /**
     * 获取Reactor线程ID.
     *
     * @return int
     */
    public function getReactorId(): int
    {
        return $this->reactorId;
    }
}
