<?php

namespace Imi\Process;

abstract class BaseProcess implements IProcess
{
    /**
     * 数据.
     *
     * @var array
     */
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
        foreach ($data as $k => $v)
        {
            $this->$k = $v;
        }
    }
}
