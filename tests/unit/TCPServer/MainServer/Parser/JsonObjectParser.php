<?php

declare(strict_types=1);

namespace Imi\Test\TCPServer\MainServer\Parser;

class JsonObjectParser extends \Imi\Server\DataParser\JsonObjectParser
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
