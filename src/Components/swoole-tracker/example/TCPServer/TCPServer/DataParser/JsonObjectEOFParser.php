<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Example\TCPServer\TCPServer\DataParser;

class JsonObjectEOFParser extends \Imi\Server\DataParser\JsonObjectParser
{
    /**
     * 编码为存储格式.
     *
     * @param mixed $data
     */
    public function encode($data): string
    {
        return json_encode($data) . "\r\n";
    }
}
