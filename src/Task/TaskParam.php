<?php

namespace Imi\Task;

use Imi\Task\Interfaces\ITaskParam;

class TaskParam implements ITaskParam
{
    protected $data = [];

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
