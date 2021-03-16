<?php

declare(strict_types=1);

namespace Imi\Cron\Message;

use Imi\Cron\Annotation\Cron;

class AddCron implements IMessage
{
    /**
     * 定时任务注解.
     */
    public Cron $cronAnnotation;

    /**
     * 任务
     */
    public string $task = '';
}
