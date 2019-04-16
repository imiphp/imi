<?php
namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\TaskCoEventParam;
use Imi\Server\Event\Param\TaskEventParam;

/**
 * 监听服务器task事件接口
 */
interface ITaskEventListener
{
    /**
     * 事件处理方法
     * @param TaskCoEventParam|TaskEventParam $e
     * @return void
     */
    public function handle($e);
}