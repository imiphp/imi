<?php

declare(strict_types=1);

namespace Imi\Cron\Listener;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Listener;
use Imi\Cron\Annotation\Cron;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER",priority=Imi\Util\ImiPriority::IMI_MIN)
 * @Listener(eventName="IMI.CO_SERVER.START",priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class Init implements IEventListener
{
    /**
     * @Inject("CronManager")
     *
     * @var \Imi\Cron\CronManager
     */
    protected $cronManager;

    /**
     * @Inject("AutoRunProcessManager")
     *
     * @var \Imi\Process\AutoRunProcessManager
     */
    protected $autoRunProcessManager;

    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        // 未启用定时任务进程不初始化
        if (!$this->autoRunProcessManager->exists('CronProcess'))
        {
            return;
        }
        $cronManager = $this->cronManager;
        foreach (AnnotationManager::getAnnotationPoints(Cron::class, 'class') as $point)
        {
            $cronManager->addCronByAnnotation($point->getAnnotation(), $point->getClass());
        }
    }
}
