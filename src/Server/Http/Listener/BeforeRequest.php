<?php
namespace Imi\Server\Http\Listener;

use Imi\RequestContext;
use Imi\Server\Event\Param\RequestEventParam;
use Imi\Server\Event\Listener\IRequestEventListener;
use Imi\App;

/**
 * request事件前置处理
 */
class BeforeRequest implements IRequestEventListener
{
    /**
     * 事件处理方法
     * @param RequestEventParam $e
     * @return void
     */
    public function handle(RequestEventParam $e)
    {
        try {
            // 上下文创建
            RequestContext::create([
                'server'    =>  $server = $e->request->getServerInstance(),
                'request'   =>  $e->request,
                'response'  =>  $e->response,
            ]);

            // 中间件
            $dispatcher = $server->getBean('HttpDispatcher');
            $dispatcher->dispatch($e->request, $e->response);
        } catch(\Throwable $th) {
            if(true !== App::getBean('HttpErrorHandler')->handle($th))
            {
                throw $th;
            }
        }
    }
}