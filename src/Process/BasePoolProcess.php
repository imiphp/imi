<?php

namespace Imi\Process;

abstract class BasePoolProcess implements IPoolProcess
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
