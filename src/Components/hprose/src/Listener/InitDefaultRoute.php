<?php

namespace Imi\Hprose\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Hprose\Route\Annotation\HproseRoute;

/**
 * @Listener("IMI.ROUTE.INIT.DEFAULT:Hprose")
 */
class InitDefaultRoute implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        $data = $e->getData();
        $this->init($data['className'], $data['classAnnotation'], $data['methodName'], $data['result']);
    }

    /**
     * 初始化.
     *
     * @param string                                  $className
     * @param \Imi\Rpc\Route\Annotation\RpcController $classAnnotation
     * @param string                                  $methodName
     * @param \Imi\Rpc\Route\Annotation\RpcRoute      $result
     *
     * @return void
     */
    private function init($className, $classAnnotation, $methodName, &$result)
    {
        $result = new HproseRoute([
            'name'      => $methodName,
        ]);
    }
}
