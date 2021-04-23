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

    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
        if ($data)
        {
            foreach ($data as $k => $v)
            {
                $this->$k = $v;
            }
        }
    }
}
