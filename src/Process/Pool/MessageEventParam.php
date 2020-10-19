<?php

namespace Imi\Process\Pool;

class MessageEventParam extends WorkerEventParam
{
    /**
     * 数据.
     *
     * @var array
     */
    protected $data;

    /**
     * Get 数据.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
