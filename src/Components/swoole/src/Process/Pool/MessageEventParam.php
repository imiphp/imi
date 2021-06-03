<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Pool;

class MessageEventParam extends WorkerEventParam
{
    /**
     * 数据.
     */
    protected array $data = [];

    /**
     * Get 数据.
     */
    public function getData(): array
    {
        return $this->data;
    }
}
