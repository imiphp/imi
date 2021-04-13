<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Server\Register;

use GatewayWorker\Register;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("WorkermanGatewayRegisterServer")
 */
class RegisterServer extends \Imi\Workerman\Server\Tcp\Server
{
    /**
     * Workerman Worker 类名.
     */
    protected string $workerClass = Register::class;

    /**
     * 获取实例化 Worker 用的协议.
     */
    protected function getWorkerScheme(): string
    {
        return 'text';
    }
}
