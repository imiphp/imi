<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\UdpServer\Controller;

use Imi\RequestContext;
use Imi\Server\UdpServer\Route\Annotation\UdpAction;
use Imi\Server\UdpServer\Route\Annotation\UdpController;
use Imi\Server\UdpServer\Route\Annotation\UdpRoute;

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

    /**
     * 测试重复路由警告.
     *
     * @UdpAction
     * @UdpRoute({"duplicated"="1"})
     *
     * @param
     *
     * @return void
     */
    public function duplicated1($data)
    {
    }

    /**
     * 测试重复路由警告.
     *
     * @UdpAction
     * @UdpRoute({"duplicated"="1"})
     *
     * @param
     *
     * @return void
     */
    public function duplicated2($data)
    {
    }
}
