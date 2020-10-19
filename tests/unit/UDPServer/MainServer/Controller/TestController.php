<?php

namespace Imi\Test\UDPServer\MainServer\Controller;

use Imi\RequestContext;
use Imi\Server\Route\Annotation\Udp\UdpAction;
use Imi\Server\Route\Annotation\Udp\UdpController;
use Imi\Server\Route\Annotation\Udp\UdpRoute;

/**
 * 数据收发测试.
 *
 * @UdpController
 */
class TestController extends \Imi\Controller\UdpController
{
    /**
     * 登录.
     *
     * @UdpAction
     * @UdpRoute({"action"="hello"})
     *
     * @return void
     */
    public function hello()
    {
        $data = $this->data->getFormatData();

        return [
            'time'              => date($data->format, $data->time),
            'middlewareData'    => RequestContext::get('middlewareData'),
        ];
    }
}
