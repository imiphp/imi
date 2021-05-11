<?php

namespace Imi\SwooleTracker\Example\TCPServer\TCPServer\DataParser;

class JsonObjectFixedParser extends \Imi\Server\DataParser\JsonObjectParser
{
    /**
     * 编码为存储格式.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function encode($data)
    {
        $content = json_encode($data);

        return pack('N', \strlen($content)) . $content;
    }

    /**
     * 解码为php变量.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function decode($data)
    {
        return json_decode(substr($data, 4));
    }
}
