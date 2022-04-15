<?php

declare(strict_types=1);

namespace Imi\Cron\Message;

class CommonMsg implements IMessage
{
    public mixed $response = [];

    public function __construct(mixed $response = [])
    {
        $this->response = $response;
    }
}
