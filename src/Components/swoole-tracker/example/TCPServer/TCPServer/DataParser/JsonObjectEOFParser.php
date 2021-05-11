<?php

namespace Imi\SwooleTracker\Example\TCPServer\TCPServer\DataParser;

class JsonObjectEOFParser extends \Imi\Server\DataParser\JsonObjectParser
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
        return json_encode($data) . "\r\n";
    }
}
