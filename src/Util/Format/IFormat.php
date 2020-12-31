<?php

declare(strict_types=1);

namespace Imi\Util\Format;

interface IFormat
{
    /**
     * 编码为存储格式.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function encode($data): string;

    /**
     * 解码为php变量.
     *
     * @param string $data
     *
     * @return mixed
     */
    public function decode(string $data);
}
