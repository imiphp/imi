<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\TcpServer\Parser;

class JsonObjectParser extends \Imi\Server\DataParser\JsonObjectParser
{
    /**
     * 编码为存储格式.
     */
    public function encode(mixed $data): string
    {
        return json_encode($data) . "\n";
    }
}
