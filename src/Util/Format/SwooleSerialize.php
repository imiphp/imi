<?php
namespace Imi\Util\Format;

/**
 * Swoole 内置序列化
 * 
 * 由于Swoole 官方不再维护序列化功能，所以将在 imi 1.0 正式版发布时弃用
 * 
 * @deprecated unknown
 */
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