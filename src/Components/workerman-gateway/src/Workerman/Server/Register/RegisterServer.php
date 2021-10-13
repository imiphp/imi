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
     * {@inheritDoc}
     */
    protected string $workerClass = Register::class;

    /**
     * {@inheritDoc}
     */
    protected function getWorkerScheme(): string
    {
        return 'text';
    }
}
