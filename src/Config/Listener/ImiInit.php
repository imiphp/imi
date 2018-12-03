<?php
namespace Imi\Config\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\App;

/**
 * @Listener(eventName="IMI.INITED",priority=PHP_INT_MAX)
 * @Listener(eventName="IMI.INIT.WORKER.BEFORE",priority=PHP_INT_MAX)
 */
class ImiInit implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        // 加载 .env 配置
        $dotenv = App::getBean('Dotenv');
        $dotenv->init();
    }

}