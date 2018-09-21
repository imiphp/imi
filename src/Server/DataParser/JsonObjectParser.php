<?php
namespace Imi\Server\DataParser;

class JsonObjectParser implements IParser
{
    /**
     * 编码为存储格式
     * @param mixed $data
     * @return mixed
     */
    public function encode($data)
    {
        return \json_encode($data);
    }

    /**
     * 解码为php变量
     * @param mixed $data
     * @return mixed
     */
    public function decode($data)
    {
        return \json_decode($data);
    }
}