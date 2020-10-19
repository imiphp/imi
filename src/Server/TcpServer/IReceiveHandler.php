<?php

namespace Imi\Server\TcpServer;

use Imi\Server\TcpServer\Message\IReceiveData;

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
