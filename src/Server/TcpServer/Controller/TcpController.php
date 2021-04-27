<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Controller;

use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\TcpServer\Message\IReceiveData;

/**
 * TCP 控制器.
 */
abstract class TcpController
{
    /**
     * 数据.
     *
     * @ServerInject("TcpReceiveDataProxy")
     */
    public IReceiveData $data;

    /**
     * 编码消息，把数据编码为发送给客户端的格式.
     *
     * @param mixed $data
     */
    protected function encodeMessage($data): string
    {
        return RequestContext::getServerBean(\Imi\Server\DataParser\DataParser::class)->encode($data);
    }
}
