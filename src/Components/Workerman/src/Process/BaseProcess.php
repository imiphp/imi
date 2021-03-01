<?php

declare(strict_types=1);

namespace Imi\Workerman\Process;

use Imi\Workerman\Process\Contract\IProcess;

abstract class BaseProcess implements IProcess
{
    /**
     * 数据.
     *
     * @var array
     */
    protected array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
        foreach ($data as $k => $v)
        {
            $this->$k = $v;
        }
    }
}
