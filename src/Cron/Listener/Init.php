<?php

declare(strict_types=1);

namespace Imi\Cron\Listener;

use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Listener;
use Imi\Cron\Annotation\Cron;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER",priority=Imi\Util\ImiPriority::IMI_MIN)
 * @Listener(eventName="IMI.CO_SERVER.START",priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class Init implements IEventListener
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
        $servers = ServerManager::getServers();
        $server = reset($servers);
        if (!$server instanceof ISwooleServer)
        {
            return;
        }
        /** @var \Imi\Cron\CronManager $cronManager */
        $cronManager = App::getBean('CronManager');
        /** @var \Imi\Swoole\Process\AutoRunProcessManager $autoRunProcessManager */
        $autoRunProcessManager = App::getBean('AutoRunProcessManager');
        // 未启用定时任务进程不初始化
        if (!$autoRunProcessManager->exists('CronProcess'))
        {
            return;
        }
        foreach (AnnotationManager::getAnnotationPoints(Cron::class, 'class') as $point)
        {
            $cronManager->addCronByAnnotation($point->getAnnotation(), $point->getClass());
        }
    }
}
