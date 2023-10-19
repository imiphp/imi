<?php

declare(strict_types=1);

namespace Imi\Swoole\Task;

use Imi\Swoole\Task\Interfaces\ITaskParam;

class TaskParam implements ITaskParam
{
    /**
     * @param mixed $data
     */
    public function __construct(protected $data = [])
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }
}
