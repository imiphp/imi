<?php

declare(strict_types=1);

namespace Imi\Swoole\Task;

use Imi\Swoole\Task\Interfaces\ITaskParam;

class TaskParam implements ITaskParam
{
    /**
     * @var mixed
     */
    protected $data = [];

    /**
     * @param mixed $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * 获取数据.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
