<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Server\Gateway;

use GatewayWorker\Gateway;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("WorkermanGatewayGatewayServer")
 */
class GatewayServer extends \Imi\Workerman\Server\Tcp\Server
{
    /**
     * Workerman Worker 类名.
     */
    protected string $workerClass = Gateway::class;
}
