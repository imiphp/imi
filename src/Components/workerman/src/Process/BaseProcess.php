<?php

declare(strict_types=1);

namespace Imi\Workerman\Process;

use Imi\Workerman\Process\Contract\IProcess;

abstract class BaseProcess implements IProcess
{
    public function __construct(
        /**
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
