<?php

declare(strict_types=1);

namespace Imi\Test\TCPServer\MainServer\Parser;

class JsonObjectParser extends \Imi\Swoole\Server\DataParser\JsonObjectParser
{
    /**
     * 编码为存储格式.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function encode($data): string
    {
        return json_encode($data) . "\r\n";
    }
}
