<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

use Imi\Swoole\Process\Contract\IProcess;

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
                if (\is_string($k) && property_exists($this, $k))
                {
                    $this->{$k} = $v;
                }
            }
        }
    }
}
