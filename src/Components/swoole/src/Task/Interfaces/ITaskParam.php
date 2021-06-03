<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Interfaces;

interface ITaskParam
{
    /**
     * 获取数据.
     *
     * @return mixed
     */
    public function getData();
}
