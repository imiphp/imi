<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\TCPServer\MainServer\Parser;

class JsonObjectParser extends \Imi\Server\DataParser\JsonObjectParser
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
