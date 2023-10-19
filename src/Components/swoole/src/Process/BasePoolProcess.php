<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

use Imi\Swoole\Process\Contract\IPoolProcess;

abstract class BasePoolProcess implements IPoolProcess
{
    public function __construct(/**
     * 数据.
     */
    protected array $data = [])
    {
        if ($data)
        {
            foreach ($data as $k => $v)
            {
                $this->{$k} = $v;
            }
        }
    }
}
