<?php

declare(strict_types=1);

namespace Imi\Util\Interfaces;

interface IArrayable
{
    /**
     * 将当前对象作为数组返回.
     */
    public function toArray(): array;
}
