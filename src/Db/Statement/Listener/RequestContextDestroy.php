<?php
namespace Imi\Db\Statement\Listener;

use Imi\Event\EventParam;
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
        StatementManager::destoryRequestContext();
    }
}