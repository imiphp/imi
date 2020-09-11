<?php

namespace Imi\Cron\Message;

class AddCron implements IMessage
{
    /**
     * 定时任务注解.
     *
     * @var \Imi\Cron\Annotation\Cron
     */
    public $cronAnnotation;

    /**
     * 任务
     *
     * @var string
     */
    public $task;
}
