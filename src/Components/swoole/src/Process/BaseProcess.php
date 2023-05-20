<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

use Imi\Swoole\Process\Contract\IProcess;

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
                if (\is_string($k) && property_exists($this, $k))
                {
                    $this->{$k} = $v;
                }
            }
        }
    }
}
