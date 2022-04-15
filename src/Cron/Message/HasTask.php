<?php

declare(strict_types=1);

namespace Imi\Cron\Message;

class HasTask implements IMessage
{
    /**
     * 任务
     */
    public string $task = '';
}
