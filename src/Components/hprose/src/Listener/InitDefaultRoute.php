<?php

declare(strict_types=1);

namespace Imi\Hprose\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Hprose\Route\Annotation\HproseRoute;
use Imi\Rpc\Route\Annotation\RpcController;
use Imi\Rpc\Route\Annotation\RpcRoute;

/**
 * @Listener("IMI.ROUTE.INIT.DEFAULT:Hprose")
 */
class InitDefaultRoute implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $data = $e->getData();
        $this->init($data['className'], $data['classAnnotation'], $data['methodName'], $data['result']);
    }

    /**
     * 初始化.
     */
    private function init(string $className, RpcController $classAnnotation, string $methodName, ?RpcRoute &$result): void
    {
        $result = new HproseRoute([
            'name'      => $methodName,
        ]);
    }
}
