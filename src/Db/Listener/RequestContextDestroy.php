<?php
namespace Imi\Db\Listener;

use Imi\RequestContext;
use Imi\Event\EventParam;
use Imi\Db\Pool\DbResource;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Db\Statement\StatementManager;

class RequestContextDestroy implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        // 释放当前请求上下文的 Statement
        StatementManager::destoryRequestContext();
        // 释放当前连接池连接的 Statement
        foreach(RequestContext::get('poolResources', []) as $resource)
        {
            if($resource instanceof DbResource)
            {
                StatementManager::unUsingAll($resource->getInstance());
            }
        }
    }
}