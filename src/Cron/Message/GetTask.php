<?php

declare(strict_types=1);

namespace Imi\Cron\Message;

class GetTask implements IMessage
{
    /**
     * 任务ID.
     */
    public string $id = '';
}
