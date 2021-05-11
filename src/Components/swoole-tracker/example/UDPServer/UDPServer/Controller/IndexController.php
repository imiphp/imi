<?php

namespace Imi\SwooleTracker\Example\UDPServer\UDPServer\Controller;

use Imi\Server\Route\Annotation\Udp\UdpAction;
use Imi\Server\Route\Annotation\Udp\UdpController;
use Imi\Server\Route\Annotation\Udp\UdpRoute;

/**
 * 数据收发测试.
 *
 * @UdpController
 */
class IndexController extends \Imi\Controller\UdpController
{
    /**
     * 登录.
     *
     * @UdpAction
     * @UdpRoute({"action"="hello"})
     *
     * @return mixed
     */
    public function hello()
    {
        return [
            'time'    => date($this->data->getFormatData()->format),
        ];
    }
}
