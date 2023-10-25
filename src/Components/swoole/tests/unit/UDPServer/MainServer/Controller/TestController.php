<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\UDPServer\MainServer\Controller;

use Imi\RequestContext;
use Imi\Server\UdpServer\Route\Annotation\UdpAction;
use Imi\Server\UdpServer\Route\Annotation\UdpController;
use Imi\Server\UdpServer\Route\Annotation\UdpRoute;

/**
 * 数据收发测试.
 */
#[UdpController]
class TestController extends \Imi\Server\UdpServer\Controller\UdpController
{
    /**
     * 登录.
     */
    #[UdpAction]
    #[UdpRoute(condition: ['action' => 'hello'])]
    public function hello(): array
    {
        $data = $this->data->getFormatData();

        return [
            'time'              => date($data->format, $data->time),
            'middlewareData'    => RequestContext::get('middlewareData'),
        ];
    }

    /**
     * 测试重复路由警告.
     */
    #[UdpAction]
    #[UdpRoute(condition: ['duplicated' => '1'])]
    public function duplicated1(): void
    {
    }

    /**
     * 测试重复路由警告.
     */
    #[UdpAction]
    #[UdpRoute(condition: ['duplicated' => '1'])]
    public function duplicated2(): void
    {
    }
}
