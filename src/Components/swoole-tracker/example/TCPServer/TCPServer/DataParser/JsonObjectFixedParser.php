<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Example\TCPServer\TCPServer\DataParser;

class JsonObjectFixedParser extends \Imi\Server\DataParser\JsonObjectParser
{
    /**
     * 编码为存储格式.
     *
     * @param mixed $data
     */
    public function encode($data): string
    {
        $content = json_encode($data);

        return pack('N', \strlen($content)) . $content;
    }

    /**
     * 解码为php变量.
     *
     * @return mixed
     */
    public function decode(string $data)
    {
        return json_decode(substr($data, 4));
    }
}
