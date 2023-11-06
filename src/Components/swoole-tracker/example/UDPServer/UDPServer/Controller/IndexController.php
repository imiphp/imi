<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Example\UDPServer\UDPServer\Controller;

use Imi\Server\UdpServer\Route\Annotation\UdpAction;
use Imi\Server\UdpServer\Route\Annotation\UdpController;
use Imi\Server\UdpServer\Route\Annotation\UdpRoute;

/**
 * 数据收发测试.
 */
#[UdpController]
class IndexController extends \Imi\Controller\UdpController
{
    /**
     * 登录.
     */
    #[UdpAction]
    #[UdpRoute(condition: ['action' => 'hello'])]
    public function hello(): array
    {
        return [
            'time'    => date($this->data->getFormatData()->format),
        ];
    }
}
