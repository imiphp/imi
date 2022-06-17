<?php

declare(strict_types=1);

namespace Imi\Cron\Listener;

use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Listener;
use Imi\Cron\Annotation\Cron;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER", priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class Init implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        if ('cli' !== \PHP_SAPI)
        {
            return;
        }
        /** @var \Imi\Process\AutoRunProcessManager $autoRunProcessManager */
        $autoRunProcessManager = App::getBean('AutoRunProcessManager');
        // 未启用定时任务进程不初始化
        if (!$autoRunProcessManager->exists('CronProcess'))
        {
            return;
        }
        /** @var \Imi\Cron\Contract\ICronManager $cronManager */
        $cronManager = App::getBean('CronManager');
        foreach (AnnotationManager::getAnnotationPoints(Cron::class, 'class') as $point)
        {
            // @phpstan-ignore-next-line
            $cronManager->addCronByAnnotation($point->getAnnotation(), $point->getClass());
        }
    }
}
