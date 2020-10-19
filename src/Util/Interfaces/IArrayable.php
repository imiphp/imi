<?php

namespace Imi\Util\Interfaces;

interface IArrayable
{
    /**
     * 将当前对象作为数组返回.
     *
     * @return array
     */
    public function toArray(): array;
}
