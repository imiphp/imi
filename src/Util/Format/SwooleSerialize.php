<?php
namespace Imi\Util\Format;

class SwooleSerialize implements IFormat
{
    /**
     * 编码为存储格式
     * @param mixed $data
     * @return mixed
     */
    public function encode($data)
    {
        return \Swoole\Serialize::pack($data);
    }

    /**
     * 解码为php变量
     * @param mixed $data
     * @return mixed
     */
    public function decode($data)
    {
        return \Swoole\Serialize::unpack($data);
    }
}