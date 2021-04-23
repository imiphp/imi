<?php

declare(strict_types=1);

namespace Imi\Workerman\Process;

use Imi\Workerman\Process\Contract\IProcess;

abstract class BaseProcess implements IProcess
{
    /**
     * 数据.
     */
    protected array $data = [];

    public function __construct(array $data = [])
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
