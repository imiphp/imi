<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Pool;

class MessageEventParam extends WorkerEventParam
{
    /**
     * 数据.
     *
     * @var array
     */
    protected array $data;

    /**
     * Get 数据.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
