<?php

declare(strict_types=1);

namespace Imi\Swoole\Task;

use Imi\Swoole\Task\Interfaces\ITaskParam;

class TaskParam implements ITaskParam
{
    public function __construct(protected mixed $data = [])
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getData(): mixed
    {
        return $this->data;
    }
}
