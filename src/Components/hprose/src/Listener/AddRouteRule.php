<?php

namespace Imi\Hprose\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Hprose\Route\Annotation\HproseRoute as HproseRouteAnnotation;
use Imi\ServerManage;
use Imi\Util\Text;

/**
 * @Listener("IMI.RPC.ROUTE.ADD_RULE:Hprose")
 */
class AddRouteRule implements IEventListener
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
        $this->addRuleAnnotation($data['annotation'], $data['callable'], $data['options']);
    }

    /**
     * 增加路由规则，直接使用注解方式.
     *
     * @param \Imi\Hprose\Route\Annotation\HproseRoute $annotation
     * @param mixed                                    $callable
     * @param array                                    $options
     *
     * @return void
     */
    private function addRuleAnnotation(HproseRouteAnnotation $annotation, $callable, $options = [])
    {
        $serverName = $options['serverName'];
        $controllerAnnotation = $options['controller'];
        /** @var \Hprose\Swoole\Socket\Service $hproseServer */
        // @phpstan-ignore-next-line
        $hproseServer = ServerManage::getServer($serverName)->getHproseService();

        // alias
        if (Text::isEmpty($controllerAnnotation->prefix))
        {
            $alias = $annotation->name;
        }
        else
        {
            $alias = $controllerAnnotation->prefix . $annotation->name;
        }

        // funcOptions
        $funcOptions = [
            'mode'          => $annotation->mode,
            'simple'        => $annotation->simple,
            'oneway'        => $annotation->oneway,
            'async'         => $annotation->async,
            'passContext'   => $annotation->passContext,
        ];

        $hproseServer->addFunction($callable, $alias, $funcOptions);
    }
}
