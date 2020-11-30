<?php

declare(strict_types=1);

namespace Imi\Process;

abstract class BasePoolProcess implements IPoolProcess
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
