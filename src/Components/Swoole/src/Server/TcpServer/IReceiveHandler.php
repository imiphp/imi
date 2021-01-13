<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer;

use Imi\Swoole\Server\TcpServer\Message\IReceiveData;

interface IReceiveHandler
{
    /**
     * 返回值为响应内容，为null则无任何响应.
     *
     * @param IReceiveData $data
     *
     * @return mixed
     */
    public function handle(IReceiveData $data);
}
