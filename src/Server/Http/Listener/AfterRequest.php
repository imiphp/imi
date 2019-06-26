<?php
namespace Imi\Server\Http\Listener;

use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\RequestEventParam;
use Imi\Server\Event\Listener\IRequestEventListener;
use Imi\App;
use Imi\ServerManage;
use Imi\Db\Pool\DbResource;
use Imi\Db\Statement\StatementManager;

/**
 * request事件后置处理
 * @ClassEventListener(className="Imi\Server\Http\Server",eventName="request",priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class AfterRequest implements IRequestEventListener
{
    /**
     * 事件处理方法
     * @param RequestEventParam $e
     * @return void
     */
    public function handle(RequestEventParam $e)
    {
        // 释放正在被使用的数据库 Statement
        foreach(RequestContext::get('poolResources', []) as $resource)
        {
            if($resource instanceof DbResource)
            {
                StatementManager::unUsingAll($resource->getInstance());
            }
        }
        // 销毁请求上下文
        RequestContext::destroy();
    }
}